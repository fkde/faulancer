<?php

namespace Faulancer\Event;

abstract class AbstractEvent
{
    private mixed $payload;

    /**
     * @param mixed $payload
     */
    public function __construct(mixed $payload = null)
    {
        $this->payload = $payload;
    }

    /**
     * @return mixed
     */
    protected function getPayload(): mixed
    {
        return $this->payload;
    }

}