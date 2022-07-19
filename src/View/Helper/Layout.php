<?php

namespace Faulancer\View\Helper;

use Faulancer\Initializer;
use Faulancer\View\Renderer;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\ContainerException;
use Faulancer\Exception\FileNotFoundException;

class Layout extends AbstractViewHelper
{
    /**
     * @param string $template
     *
     * @return void
     *
     * @throws FileNotFoundException
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function __invoke(string $template): void
    {
        /** @var Renderer $parentRenderer */
        $parentRenderer = Initializer::load(Renderer::class);
        //$parentRenderer->reset();
        $parentRenderer->setTemplate($template);

        $this->getRenderer()->setParentView($parentRenderer);
    }
}
