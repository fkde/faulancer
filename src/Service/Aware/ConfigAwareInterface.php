<?php

namespace Faulancer\Service\Aware;

use Faulancer\Config;

interface ConfigAwareInterface
{
    /**
     * @return Config
     */
    public function getConfig(): Config;

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void;
}
