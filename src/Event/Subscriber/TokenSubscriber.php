<?php

namespace Faulancer\Event\Subscriber;

use Faulancer\Event\AbstractSubscriber;
use Faulancer\Event\RequestEvent;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\LoggerAwareTrait;

class TokenSubscriber extends AbstractSubscriber implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @return string[]
     */
    public static function subscribe(): array
    {
        return [
            RequestEvent::class => 'onRequest'
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        $token = $event->getRequest()->getHeader('x-token');

        if (null === $token) {
            $this->getLogger()->debug('No token in request detected.');
            return;
        }


    }
}