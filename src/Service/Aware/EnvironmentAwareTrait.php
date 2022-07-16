<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\Environment;

trait EnvironmentAwareTrait
{

    protected Environment $environment;

    /**
     * @param Environment $environment
     */
    public function setEnvironment(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

}