<?php

namespace Faulancer\Service;

class Environment
{

    private string $environment;

    /**
     * @param string $environment
     */
    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->environment;
    }
}
