<?php

namespace Faulancer\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends FrameworkException implements NotFoundExceptionInterface
{
}
