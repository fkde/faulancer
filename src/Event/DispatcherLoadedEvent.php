<?php

namespace Faulancer\Event;

use Faulancer\Dispatcher;

class DispatcherLoadedEvent extends AbstractEvent
{
    private Dispatcher $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        parent::__construct($dispatcher);
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return string
     */
    public static function getName(): string
    {
        return 'dispatcher.loaded';
    }

    /**
     * @return Dispatcher
     */
    public function getDispatcher(): Dispatcher
    {
        return $this->dispatcher;
    }
}