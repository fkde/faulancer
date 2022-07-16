<?php

namespace Faulancer\Service\Aware;

use Psr\Log\LoggerInterface;

interface LoggerAwareInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger);

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;
}
