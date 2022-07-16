<?php

namespace Faulancer\View\Helper;

use Faulancer\Exception\ViewHelperException;

class Link extends AbstractViewHelper
{
    /**
     * @param string $routeName
     * @param array  $attributes
     * @param string $linkTextAdditional
     *
     * @return string
     *
     * @throws ViewHelperException
     */
    public function __invoke(string $routeName, array $attributes = [], string $linkTextAdditional = ''): string
    {
        if (empty($routeName)) {
            throw new ViewHelperException('The routeName must not be empty.');
        }

        $routes = $this->getConfig()->get('routes');

        if (empty($routes[$routeName])) {
            $this->getLogger()->error(
                sprintf('Could not render Link named "%s". Did you forget to create the Route?', $routeName),
                ['class' => get_class($this)]);
            return '';
        }

        $path = $routes[$routeName]['path'] ?? null;
        $link = $routes[$routeName]['link'];

        if (is_array($path)) {
            try {
                $lang = $this->getConfig()->get('language') ?? 'de';
                $path = $routes[$routeName]['path'][$lang];
            } catch (\Error $e) {
                $this->getLogger()->error(
                    'Couldn\'t read language variant from routes. Did you add the language array to both, link and path?'
                );
            }
        }

        $linkText = $this->getTranslator()->translate($link);

        if ($_SERVER['REQUEST_URI'] === $path) {
            $attributes['class'] = $attributes['class'] ?? ' active';
        }

        $attributes['data-text'] = $linkText;

        $attributeString  = '';
        $attributePattern = ' %s="%s" ';

        foreach ($attributes as $attr => $value) {
            $attributeString .= sprintf($attributePattern, $attr, $value);
        }

        $attributeString = substr($attributeString, 1);

        return '<a href="' . $path . '" ' . $attributeString . '>' . sprintf($linkText, $linkTextAdditional) . '</a>';
    }
}