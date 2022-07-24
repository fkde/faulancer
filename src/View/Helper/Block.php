<?php

namespace Faulancer\View\Helper;

class Block extends AbstractViewHelper
{
    /**
     * @param string $name
     */
    public function __invoke(string $name)
    {
        if ($this->getRenderer()->getParentView()->hasVariable($name)) {
            $this->getLogger()->error('Opening block view helper failed. Expected block variable is already present.');
            return;
        }

        $this->getRenderer()->getParentView()->setVariable($name, 'init');
        $this->getLogger()->debug('Opening output buffer for block "' . $name . '".');
        ob_start();
    }
}
