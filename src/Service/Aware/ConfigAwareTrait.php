<?php

namespace Faulancer\Service\Aware;

use Faulancer\Config;

trait ConfigAwareTrait
{

    private Config $config;

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config): void
    {
        $this->config = $config;
    }
}
