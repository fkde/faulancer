<?php

namespace Faulancer\View\Helper;

use Faulancer\Service\Aware\ConfigAwareInterface;
use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Service\Aware\RequestAwareInterface;
use Faulancer\Service\Aware\RequestAwareTrait;

class Path extends AbstractViewHelper implements ConfigAwareInterface, RequestAwareInterface
{
    use ConfigAwareTrait;
    use RequestAwareTrait;

    /**
     * @param string|null $routeName
     * @param array|null  $attributes
     *
     * @return string|null|self
     */
    public function __invoke(string $routeName = null, ?array $attributes = []): string|static|null
    {
        if (null === $routeName) {
            return $this;
        }

        $routes = $this->getConfig()->get('routes');
        $lang   = $this->getConfig()->get('language');

        $path = is_array($routes[$routeName]['path'])
            ? $routes[$routeName]['path'][$lang] ?? null
            : $routes[$routeName]['path'] ?? null;

        if ('index' === $routeName) {
            $routeName = 'index_' . $lang;
            $path = $routes[$routeName]['path'][$lang] ?? null;
        }

        if (null === $path) {
            $this->getLogger()->error('Route "' . $routeName . '" not found.', ['class' => get_class($this)]);
            return null;
        }

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $path = str_replace('/:' . $key, '/' . $value, $path);
            }
        }

        return $path;
    }

    /**
     * @param string $lang
     *
     * @return string|null
     */
    public function getCurrentPathByLanguage(string $lang):? string
    {
        $result = null;
        $currentPath = $this->getRequest()->getUri()->getPath();

        $routes = $this->getConfig()->get('routes');

        foreach ($routes as $name => $route) {
            if (is_array($route['path']) && in_array($currentPath, $route['path'])) {
                $result = $route['path'][$lang] ?? null;
                break;
            }
        }

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $result = str_replace('/:' . $key, '/' . $value, $result);
            }
        }

        return $result;
    }
}
