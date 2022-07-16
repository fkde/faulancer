<?php

namespace Faulancer\View\Helper;

class RenderBlock extends AbstractViewHelper
{
    /**
     * @param string $name
     * @param string|null $defaultValue
     * @return array|string
     */
    public function __invoke(string $name, ?string $defaultValue = ''): array|string
    {
        $value = $this->getRenderer()->getVariable($name);

        if (empty($value)) {
            $value = $defaultValue;
        }

        return $value;
    }
}
