<?php

namespace Faulancer\Service\Aware;

use Faulancer\Event\Observer;

interface ObserverAwareInterface
{

    /**
     * @return Observer
     */
    public function getObserver(): Observer;

    /**
     * @param Observer $observer
     * @return void
     */
    public function setObserver(Observer $observer): void;
}