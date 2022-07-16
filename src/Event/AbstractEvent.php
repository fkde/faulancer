<?php

namespace Faulancer\Event;

abstract class AbstractEvent
{
    public const NAME = null;

    private null|object|string|array|int $payload;

    /**
     * @param null|object|string|array|int $payload
     */
    public function __construct(null|object|string|array|int $payload = null)
    {
        $this->payload = $payload;
    }

    /**
     * @return object|string|array|int|null
     */
    protected function getPayload(): null|object|string|array|int
    {
        return $this->payload;
    }



}