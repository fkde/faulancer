<?php

namespace Faulancer\Event;

use Throwable;

class ExceptionEvent extends AbstractEvent
{
    private Throwable $throwable;

    public static function getName(): string
    {
        return 'exception.thrown';
    }

    /**
     * @param Throwable $throwable
     */
    public function __construct(Throwable $throwable)
    {
        parent::__construct($throwable);
        $this->throwable = $throwable;
    }

    /**
     * @return Throwable
     */
    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }
}