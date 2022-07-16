<?php

namespace Faulancer\View\Helper;

use Faulancer\Service\Environment as EnvironmentService;

class Environment extends AbstractViewHelper
{
    /**
     * @return EnvironmentService
     */
    public function __invoke(): EnvironmentService
    {
        return $this->getEnvironment();
    }
}
