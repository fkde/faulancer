<?php

namespace Faulancer\Event;

use Faulancer\Exception\ContainerException;
use Faulancer\Exception\NotFoundException;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\LoggerAwareTrait;

class Observer implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private static int $interruptCounter = 0;

    private array $subscribers = [];

    /**
     * @param AbstractSubscriber $subscriber
     * @return void
     */
    public function addSubscriber(AbstractSubscriber $subscriber): void
    {
        // Call the setup method to get the subscribed events
        $events = $subscriber::subscribe();

        /**
         * @var string $event  The event name to which should be subscribed to
         * @var string $method The method from the subscriber which should be called
         */
        foreach ($events as $event => $method) {
            $this->subscribers[$event][$method][] = $subscriber;
        }
    }

    /**
     * @param AbstractEvent $event
     * @return void|object|string|array|int
     */
    public function notify(AbstractEvent $event): object|string|array|int|null
    {
        if ($event instanceof InterruptEvent) {
            static::$interruptCounter++;
            if (static::$interruptCounter > 1) {
                $this->getLogger()->info('Interrupt loop detected. Abort.');
                return null;
            }
        }

        $eventThrownName = get_class($event);

        try {
            foreach ($this->subscribers as $eventName => $payload) {

                // Search for matching events
                if ($eventName !== $eventThrownName) {
                    continue;
                }

                // Event found, notify subscribers
                foreach ($payload as $method => $subscriberList) {
                    foreach ($subscriberList as $subscriber) {
                        $result = $subscriber->$method($event);
                        if (null !== $result) {
                            self::notify(new InterruptEvent($result));
                            return $result;
                        }
                    }
                }
            }
        } catch (ContainerException | NotFoundException $e) {
            $this->getLogger()->warning($e->getMessage(), ['exception' => $e]);
        }

        return null;
    }
}