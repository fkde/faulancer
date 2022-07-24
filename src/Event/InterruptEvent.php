<?php

namespace Faulancer\Event;

class InterruptEvent extends AbstractEvent
{

    /**
     * @param mixed|null $payload
     */
    public function __construct(mixed $payload = null)
    {
        parent::__construct($payload);
    }
}