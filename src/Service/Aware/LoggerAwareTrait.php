<?php

namespace Faulancer\Service\Aware;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerAwareTrait
 * @package Hardkode\Service
 */
trait LoggerAwareTrait
{

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

}