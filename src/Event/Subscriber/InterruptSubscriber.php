<?php

namespace Faulancer\Event\Subscriber;

use Faulancer\Event\AbstractSubscriber;
use Faulancer\Event\InterruptEvent;

class InterruptSubscriber extends AbstractSubscriber
{
    public static function subscribe(): array
    {
        return [
            InterruptEvent::class => 'onInterrupt'
        ];
    }

    public function onInterrupt($payload)
    {
        return 'Hallo';
    }

}