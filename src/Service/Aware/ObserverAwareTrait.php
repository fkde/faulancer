<?php

namespace Faulancer\Service\Aware;

use Faulancer\Event\Observer;

trait ObserverAwareTrait
{

    private Observer $observer;

    /**
     * @return Observer
     */
    public function getObserver(): Observer
    {
        return $this->observer;
    }

    /**
     * @param Observer $observer
     */
    public function setObserver(Observer $observer): void
    {
        $this->observer = $observer;
    }

}