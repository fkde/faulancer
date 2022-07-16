<?php

namespace Faulancer\View\Helper;

use Faulancer\Config;
use Faulancer\View\Renderer;
use Faulancer\Service\Aware\UserAwareTrait;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Aware\SessionAwareTrait;
use Faulancer\Service\Aware\UserAwareInterface;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\TranslatorAwareTrait;
use Faulancer\Service\Aware\EnvironmentAwareTrait;
use Faulancer\Service\Aware\SessionAwareInterface;
use Faulancer\Service\Aware\TranslatorAwareInterface;
use Faulancer\Service\Aware\EnvironmentAwareInterface;

abstract class AbstractViewHelper implements
    LoggerAwareInterface,
    SessionAwareInterface,
    UserAwareInterface,
    TranslatorAwareInterface,
    EnvironmentAwareInterface
{
    use UserAwareTrait;
    use LoggerAwareTrait;
    use SessionAwareTrait;
    use TranslatorAwareTrait;
    use EnvironmentAwareTrait;

    private Renderer $renderer;

    private Config $config;

    /**
     * @param Renderer   $renderer
     * @param Config     $config
     */
    public function __construct(Renderer $renderer, Config $config)
    {
        $this->renderer = $renderer;
        $this->config   = $config;
    }

    /**
     * @return Renderer
     */
    public function getRenderer(): Renderer
    {
        return $this->renderer;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }
}
