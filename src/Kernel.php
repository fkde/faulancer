<?php

namespace Faulancer;

use \Throwable;
use Apix\Log\Logger;
use Assert\Assertion;
use Nyholm\Psr7\Request;
use Faulancer\Service\User;
use Psr\Log\LoggerInterface;
use Faulancer\Event\Observer;
use Faulancer\Service\Session;
use Faulancer\Http\HttpFactory;
use Faulancer\Event\RequestEvent;
use Faulancer\Service\Translator;
use Faulancer\Service\Environment;
use Assert\AssertionFailedException;
use Faulancer\Database\EntityManager;
use Faulancer\Event\ConfigLoadedEvent;
use Psr\Http\Message\RequestInterface;
use Faulancer\Event\AbstractSubscriber;
use Psr\Http\Message\ResponseInterface;
use Faulancer\Controller\ErrorController;
use Faulancer\Event\DispatcherLoadedEvent;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Exception\ContainerException;
use Faulancer\Exception\FrameworkException;
use Nyholm\Psr7Server\ServerRequestCreator;

class Kernel
{

    /**
     * @return array
     * @throws Exception\ContainerException
     * @throws Exception\NotFoundException
     */
    public static function bootDefaults(): array
    {
        $container = new Container();
        Initializer::setContainer($container);

        /** @var Config $config */
        $config = Initializer::load(Config::class);
        $container->set(Config::class, $config);

        $environment = getenv('APPLICATION_ENV') ?: 'production';
        $environmentService = new Environment($environment);
        $container->set(Environment::class, $environmentService);

        $logFile = sprintf('%s/%s.log', rtrim(realpath($config->get('app:logs:path')), '/'), $environment);
        $fileLogger = new Logger\File($logFile);
        $fileLogger->setMinLevel($config->get('app:logs:minLevel') ?? 'notice');
        $logger = new Logger([$fileLogger]);

        $container->set(Logger::class, $logger, [LoggerInterface::class]);

        /** @var Observer $observer */
        $observer = Initializer::load(Observer::class);
        self::registerSubscribers($observer, $logger);
        $observer->notify(new ConfigLoadedEvent($config));

        //$this->loadServices($config, $container);

        $entityManager = Initializer::load(EntityManager::class, [$config]);
        $container->set(EntityManager::class, $entityManager);

        return [
            $config,
            $entityManager,
            $logger,
            $container,
            $observer
        ];
    }

    /**
     * @return void
     * @throws Exception\FileNotFoundException
     * @throws Exception\ViewHelperException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws Throwable
     */
    public static function boot(): void
    {
        $logger = null;
        $errorController = null;

        try {
            [$config, $entityManager, $logger, $container, $observer] = static::bootDefaults();

            $httpFactory = new HttpFactory();
            $creator = new ServerRequestCreator(
                $httpFactory,
                $httpFactory,
                $httpFactory,
                $httpFactory
            );
            $request = $creator->fromGlobals();
            $container->set(Request::class, $request, [RequestInterface::class]);
            $container->set(HttpFactory::class, $httpFactory);
            $observer->notify(new RequestEvent($request));

            $translator = Initializer::load(Translator::class);
            $container->set(Translator::class, $translator);

            $session = Initializer::load(Session::class);
            $container->set(Session::class, $session);

            $user = Initializer::load(User::class, [$entityManager, $session]);
            $container->set(User::class, $user);

            /** @var ErrorController $errorController */
            $errorController = Initializer::load(ErrorController::class, [
                $request,
                $config,
                $logger,
                $entityManager,
                $session
            ]);

            set_error_handler([$errorController, 'onError']);
            //set_exception_handler([$errorController, 'onException']);

            /** @var Dispatcher $dispatcher */
            $dispatcher = Initializer::load(Dispatcher::class, [$config]);
            $observer->notify(new DispatcherLoadedEvent($dispatcher));

            $response = $dispatcher->forward($request);

            Assertion::isInstanceOf($response, ResponseInterface::class);

            $headers = $response->getHeaders();
            $statusCode = $response->getStatusCode();

            header('HTTP/2 ' . $statusCode . ' ' . $response->getReasonPhrase());

            foreach ($headers as $name => $value) {
                header($name . ': ' . implode(';', $value));
            }

            echo $response->getBody();

        } catch (NotFoundException $e) {
            header('HTTP/2 404 Not found');
            echo null !== $errorController
                ? $errorController->onException($e)
                : $e->getMessage();
        } catch (AssertionFailedException | FrameworkException $e) {
            header('HTTP/2 500 Server error');
            echo null !== $errorController
                ? $errorController->onException($e)
                : $e->getMessage();
        } catch (\ParseError | \Error $p) {
            header('HTTP/2 500 Server error');
            echo null !== $errorController
                ? $errorController->onError($p->getCode(), $p->getMessage(), $p->getFile(), $p->getLine())
                : $p->getMessage();
        } catch (\Throwable $t) {
            header('HTTP/2 500 Server error');
            echo null !== $errorController
                ? $errorController->onError($t->getCode(), $t->getMessage(), $t->getFile(), $t->getLine())
                : $t->getMessage();
        }

    }

    /**
     * @param Observer $observer
     * @param Logger   $logger
     *
     * @return void
     */
    private static function registerSubscribers(Observer $observer, Logger $logger): void
    {
        $coreDir = __DIR__ . '/Event/Subscriber';
        $appDir  = realpath('./../src/Event/Subscriber');

        // Add FQDN namespaces to found subscribers
        $coreSubscriber = array_map(fn($item) => ('Faulancer\Event\Subscriber\\' . $item), self::loadSubscribers($coreDir));
        $appSubscriber  = array_map(fn($item) => ('App\Event\Subscriber\\' . $item), self::loadSubscribers($appDir));

        $subscribers = array_merge($coreSubscriber, $appSubscriber);

        try {
            foreach ($subscribers as $subscriber) {

                $subscriberInstance = Initializer::load($subscriber);

                if (!$subscriberInstance instanceof AbstractSubscriber) {
                    continue;
                }

                $observer->addSubscriber($subscriberInstance);
            }
        } catch (NotFoundException | ContainerException $e) {
            $logger->error($e->getMessage(), ['exception' => $e]);
        }
    }

    /**
     * @param string $directory
     * @return array|null
     */
    private static function loadSubscribers(string $directory):? array
    {
        if (!is_dir($directory)) {
            return [];
        }

        return array_map(
            fn($item) => substr($item, 0, -4),
            array_filter(
                array_diff(
                    scandir($directory), ['.', '..']
                ),
                fn($item) => str_ends_with($item, 'Subscriber.php')
            )
        );
    }

    /**
     * @param Config $config
     * @param Container $container
     *
     * @return void
     *
     * @throws ContainerException
     * @throws NotFoundException
     */
    private static function loadServices(Config $config, Container $container): void
    {
        $services = $config->get('services') ?? [];

        foreach ($services as $serviceId => $serviceDefinition) {
            if (is_int($serviceId)) {
                $serviceId = $serviceDefinition;
            }

            if ($container->has($serviceId)) {
                continue;
            }

            $arguments = array_map(static function ($dependency) use ($container) {

                return ($container->has($dependency))
                    ? $container->get($dependency)
                    : Initializer::load($dependency);

            }, $serviceDefinition['arguments'] ?? []);

            $serviceObject = Initializer::load($serviceId, $arguments);

            if (null === $serviceObject) {
                continue;
            }

            $container->set($serviceId, $serviceObject);
        }
    }
}
