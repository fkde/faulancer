<?php

namespace Faulancer\Exception;

use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends FrameworkException implements NotFoundExceptionInterface
{
    public function __construct(string $message = "", array $context = [], int $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $context, $code, $previous);
    }
}
