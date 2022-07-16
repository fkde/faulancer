<?php

namespace Faulancer\Controller;

use Assert\Assert;
use Apix\Log\Logger;
use Faulancer\Config;
use Faulancer\View\Renderer;
use Faulancer\Model\Role;
use Faulancer\Initializer;
use Faulancer\Service\Aware\HttpFactoryAwareInterface;
use Faulancer\Service\Aware\HttpFactoryAwareTrait;
use ORM\Exception\NoEntity;
use Faulancer\Service\Session;
use Faulancer\Database\EntityManager;
use Psr\Http\Message\RequestInterface;
use ORM\Exception\IncompletePrimaryKey;
use Psr\Http\Message\ResponseInterface;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Form\AbstractBuilder;
use Faulancer\Exception\PermissionException;
use Faulancer\Exception\ViewHelperException;
use Psr\Http\Message\ServerRequestInterface;
use Faulancer\Exception\FileNotFoundException;

abstract class AbstractController implements HttpFactoryAwareInterface
{
    use HttpFactoryAwareTrait;

    private RequestInterface|ServerRequestInterface $request;

    private Logger $logger;

    private Config $config;

    private Session $session;

    private EntityManager $em;

    private array $renderer = [];

    /**
     * @param RequestInterface $request
     * @param Config           $config
     * @param Logger           $logger
     * @param EntityManager    $entityManager
     * @param Session          $session
     */
    public function __construct(
        RequestInterface $request,
        Config $config,
        Logger $logger,
        EntityManager $entityManager,
        Session $session
    ) {
        $this->request = $request;
        $this->config  = $config;
        $this->logger  = $logger;
        $this->em      = $entityManager;
        $this->session = $session;
    }

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->logger;
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @return Session
     */
    protected function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->em;
    }

    /**
     * @return Config
     */
    protected function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param string $template
     * @param array $variables
     * @return ResponseInterface
     *
     * @throws FileNotFoundException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws ViewHelperException
     * @throws \Throwable
     */
    protected function render(string $template, array $variables = []): ResponseInterface
    {
        $this->logger->debug('Start rendering of template "' . $template . '".');

        try {
            $this->getRenderer()->setTemplate($template);
            $this->getRenderer()->setVariables($variables);

            $this->addDefaultAssets();

            $result = $this->getRenderer()->render();

            $this->logger->debug('Successfully rendered template "' . $template . '".');
        } catch (\Throwable $t) {
            $this->logger->error($t->getMessage(), ['exception' => $t]);
            throw $t;
        }

        return $this->createResponse($result);
    }

    /**
     * @param array $data
     * @return ResponseInterface
     */
    protected function renderJson(array $data = []): ResponseInterface
    {
        try {
            $json = json_encode($data, JSON_THROW_ON_ERROR);
            return $this->createResponse($json, 'application/json');
        } catch (\JsonException $e) {
            return $this->createResponse($e->getMessage(), 500);
        }
    }

    /**
     * @param string $routeName
     *
     * @return ResponseInterface
     */
    protected function redirect(string $routeName): ResponseInterface
    {
        $path = $this->getConfig()->get('routes')[$routeName]['path'] ?? null;

        if (null === $path) {
            $path = $routeName;
        }

        $response = $this->getHttpFactory()->createResponse(301);
        return $response->withHeader('Location', $path);
    }

    /**
     * @param string $className
     * @return AbstractBuilder
     *
     * @throws NotFoundException
     */
    protected function createForm(string $className): AbstractBuilder
    {
        Assert::that($className)->classExists();
        /** @var AbstractBuilder $form */
        $form = Initializer::load($className, [$this->request]);
        return $form->build();
    }

    /**
     * @return Renderer
     *
     * @throws NotFoundException
     */
    protected function getRenderer(): Renderer
    {
        $identifier = get_called_class();

        if (empty($this->renderer[$identifier])) {
            $renderer = Initializer::load(Renderer::class);
            $this->renderer[$identifier] = $renderer;
        }

        return $this->renderer[$identifier];
    }

    /**
     * @param string $role
     *
     * @throws PermissionException
     */
    protected function requiresPermission(string $role)
    {
        try {
            $roles = $this->getEntityManager()->fetch(Role::class)->all();
        } catch (IncompletePrimaryKey | NoEntity $e) {
            $this->getLogger()->error($e->getMessage(), ['exception' => $e]);
            throw new PermissionException($e->getMessage());
        }

        $roleNames = array_map(function (Role $role) {
            return $role->name;
        }, $roles);

        Assert::that($role)->inArray($roleNames);

        $userRole = $this->getSession()->get('userRole');

        Assert::that($userRole)->notNull(function () {
            $this->getSession()->set('referer', $this->getRequest()->getUri()->getPath());
            $this->getLogger()->info('Role entry not found in Users session. Redirecting to login...');
            $this->redirect('login');
        });

        if ($userRole === 'admin') {
            return;
        }

        if ($role !== $userRole) {
            throw new PermissionException(
                sprintf(
                    'Invalid permissions for user "%s" for route "%s"',
                    $this->getSession()->get('userName'),
                    $this->getRequest()->getUri()->getPath()
                )
            );
        }
    }

    /**
     * @return void
     */
    protected function addDefaultAssets()
    {
        // Must be implemented by children
    }

    /**
     * @param string $contents
     * @param string $contentType
     * @param int    $code
     * @return ResponseInterface
     */
    private function createResponse(
        string $contents = '',
        string $contentType = 'text/html',
        int $code = 200
    ): ResponseInterface {
        $httpFactory = $this->getHttpFactory();
        $response    = $httpFactory->createResponse($code);
        $body        = $httpFactory->createStream($contents);
        return $response->withBody($body)->withHeader('Content-Type', $contentType);
    }
}
