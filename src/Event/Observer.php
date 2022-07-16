<?php

namespace Faulancer\Event;

use Faulancer\Exception\ContainerException;
use Faulancer\Exception\NotFoundException;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\LoggerAwareTrait;

class Observer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private array $subscribers = [];

    /**
     * @param AbstractSubscriber $subscriber
     * @return void
     */
    public function addSubscriber(AbstractSubscriber $subscriber): void
    {
        // Call the setup method to get the subscribed events
        $events = $subscriber::subscribe();

        foreach ($events as $event => $method) {
            $this->subscribers[$event::NAME][$method][] = $subscriber;
        }
    }

    /**
     * @param AbstractEvent $event
     * @return void|object|string|array|int
     */
    public function notify(AbstractEvent $event): object|string|array|int|null
    {
        try {
            foreach ($this->subscribers as $eventName => $payload) {

                // Search for matching events
                if ($eventName !== $event::NAME) {
                    continue;
                }

                // Event found, notify subscribers
                foreach ($payload as $method => $subscriberList) {
                    foreach ($subscriberList as $subscriber) {
                        $result = $subscriber->$method($event);

                        if (null !== $result) {
                            return $result;
                        }
                    }
                }
            }
        } catch (ContainerException | NotFoundException $e) {
            $this->getLogger()->info($e->getMessage(), ['exception' => $e]);
        }

        return null;
    }
}