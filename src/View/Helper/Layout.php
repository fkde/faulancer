<?php

namespace Faulancer\View\Helper;

use Faulancer\Exception\FileNotFoundException;

class Layout extends AbstractViewHelper
{
    /**
     * @param string $template
     *
     * @throws FileNotFoundException
     */
    public function __invoke(string $template)
    {
        $parentRenderer = clone $this->getRenderer();
        $parentRenderer->reset();
        $parentRenderer->setTemplate($template);

        $this->getRenderer()->setParentView($parentRenderer);
    }
}
