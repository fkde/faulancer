<?php

namespace Faulancer\Event;

abstract class AbstractSubscriber
{
    /**
     * Decide to which events you want to subscribe.
     *
     * @return array
     */
    abstract public static function subscribe(): array;
}