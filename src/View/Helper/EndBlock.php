<?php

namespace Faulancer\View\Helper;

class EndBlock extends AbstractViewHelper
{
    /**
     * @param string $name
     */
    public function __invoke(string $name)
    {
        if (
            false === $this->getRenderer()->getParentView()->hasVariable($name)
            || 'init' !== $this->getRenderer()->getParentView()->getVariable($name)
        ) {
            $this->getLogger()->error('Sealing block view helper failed. Expected block variable is missing.');
            return;
        }

        $content = ob_get_contents();
        ob_end_clean();
        $this->getRenderer()->getParentView()->setVariable($name, $content);
    }
}
