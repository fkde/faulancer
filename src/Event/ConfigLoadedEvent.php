<?php

namespace Faulancer\Event;

use Assert\Assert;
use Faulancer\Config;
use Faulancer\Exception\EventException;

class ConfigLoadedEvent extends AbstractEvent
{

    private Config $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        parent::__construct($config);
        $this->config = $config;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

}