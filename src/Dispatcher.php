<?php

namespace Faulancer;

use Assert\Assertion;
use Faulancer\Exception\FrameworkException;
use Faulancer\Service\User;
use Faulancer\Service\Session;
use Assert\AssertionFailedException;
use Faulancer\Database\EntityManager;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Faulancer\Exception\NotFoundException;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Aware\SessionAwareTrait;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\SessionAwareInterface;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use Faulancer\Service\Aware\EntityManagerAwareInterface;

class Dispatcher implements EntityManagerAwareInterface, LoggerAwareInterface, SessionAwareInterface
{
    use EntityManagerAwareTrait;
    use SessionAwareTrait;
    use LoggerAwareTrait;

    private Config $config;

    private Session $session;

    private EntityManager $em;

    private User $user;

    private ContainerInterface $container;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws AssertionFailedException
     * @throws Exception\ContainerException
     * @throws NotFoundException
     */
    public function forward(RequestInterface $request): ResponseInterface
    {
        $path   = $this->sanitizePath($request->getUri()->getPath());
        $result = $this->getRouteItemByPath($path);

        if (empty($result)) {
            throw new NotFoundException(sprintf('No matching route for path "%s" found.', $path));
        }

        $controller = Initializer::load($result['class'], [
            $request,
            $this->config,
            $this->logger,
            $this->entityManager,
            $this->session
        ]);

        Assertion::methodExists(
            $result['action'],
            $controller,
            sprintf('Method "%s" not found in class "%s"', $result['action'], $result['class'])
        );

        return call_user_func_array([$controller, $result['action']], array_values($result['parameters']));
    }

    /**
     * @param string $path
     * @return string
     */
    private function sanitizePath(string $path): string
    {
        return htmlspecialchars($path);
    }

    /**
     * @param string $path
     * @return array
     */
    private function getRouteItemByPath(string $path): array
    {
        foreach ($this->config->get('routes') as $name => $route) {

            if (empty($route['path']) || false === $this->hasPathMatch($path, $route['path'])) {
                continue;
            }

            $this->logger->debug('Matched route "' . $name . '" for path "' . $path . '".');
            $segments = $this->getPathSegments($path, $route['path']);

            if (false === $this->hasConstraintMatch($segments, $route['constraints'] ?? [])) {
                continue;
            }

            $this->logger->debug('Matched route constraints for path "' . $path . '".');

            array_shift($segments);

            return $route + ['parameters' => $segments];

        }

        return [];
    }

    /**
     * @param string $path
     * @param string|array $configPath
     * @return bool
     */
    private function hasPathMatch(string $path, string|array $configPath): bool
    {
        $matches = $this->matchPath($path, $configPath);
        return !empty($matches[0]);
    }

    /**
     * @param array $pathParameters
     * @param array $constraints
     * @return bool
     */
    private function hasConstraintMatch(array $pathParameters, array $constraints): bool
    {
        $result = [];

        foreach ($pathParameters as $key => $value) {

            $constraint = $constraints[$key] ?? null;

            if (null !== $constraint) {
                $result[] = (bool)preg_match('/' . $constraint . '/', $value);
            }

        }

        return !in_array(false, $result, true);
    }

    /**
     * @param $path
     * @param $configPath
     * @return array
     */
    private function getPathSegments($path, $configPath): array
    {
        $pathMatch   = $this->matchPath($path, $configPath);
        $matchedPath = array_shift($pathMatch);

        if (null === $matchedPath) {
            return [];
        }

        $result = [$matchedPath];

        foreach ($pathMatch as $key => $value) {
            if (!is_numeric($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string|array $configPath
     *
     * @return array
     */
    private function matchPath(string $path, string|array $configPath): array
    {
        $matches = [];

        if (is_array($configPath)) {
            return $this->matchLanguagePath($path, $configPath);
        }

        $pathRegex = $this->convertPathToRegex($configPath);
        preg_match($pathRegex, urldecode($path), $matches);

        return $matches;
    }

    /**
     * @param string $path
     * @param array $configPaths
     *
     * @return array
     */
    private function matchLanguagePath(string $path, array $configPaths): array
    {
        foreach ($configPaths as $lang => $configPath) {
            if ($matches = $this->matchPath($path, $configPath)) {
                $this->config->setLanguage($lang);
                return $matches;
            }
        }
        return [];
    }

    /**
     * @param string $configPath
     * @return string
     */
    private function convertPathToRegex(string $configPath): string
    {
        $pathRegex  = preg_replace_callback('/\/:\w+/u', static function($item) {
            $str = substr($item[0], 2);
            return '/(?<' . $str . '>[a-zäöüA-ZÄÖÜ0-9~_\-\.\:\@]+)';
        }, $configPath);

        return '/^' . str_replace('/', '\/', $pathRegex) . '$/u';
    }

}