<?php

namespace Faulancer\Service;

class Environment
{

    public const PRODUCTION = 'production';
    public const DEVELOPMENT = 'development';

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
