<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\Environment;

interface EnvironmentAwareInterface
{

    /**
     * @param Environment $environment
     *
     * @return void
     */
    public function setEnvironment(Environment $environment);

    /**
     * @return Environment
     */
    public function getEnvironment(): Environment;

}