<?php

namespace Faulancer\View\Helper;

use Assert\Assertion;
use Faulancer\Initializer;
use Faulancer\View\Renderer;
use Assert\AssertionFailedException;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Exception\ContainerException;
use Faulancer\Exception\ViewHelperException;
use Faulancer\Exception\FileNotFoundException;

class TemplateComponent extends AbstractViewHelper
{

    /**
     * @param string $component
     * @param array  $variables
     *
     * @return string
     *
     * @throws AssertionFailedException
     * @throws ContainerException
     * @throws FileNotFoundException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws ViewHelperException
     */
    public function __invoke(string $component, array $variables = []): string
    {
        Assertion::notEmpty($component);

        $componentFile = sprintf('/components/%s.phtml', $component);

        /** @var Renderer $renderer */
        $renderer = Initializer::load(Renderer::class, [], true);

        return $renderer
            ->setTemplate($componentFile)
            ->setVariables($variables)
            ->render();
    }

}