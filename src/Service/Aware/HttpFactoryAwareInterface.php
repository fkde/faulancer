<?php

namespace Faulancer\Service\Aware;

use Faulancer\Http\HttpFactory;

interface HttpFactoryAwareInterface
{
    /**
     * @return HttpFactory
     */
    public function getHttpFactory(): HttpFactory;

    /**
     * @param HttpFactory $httpFactory
     */
    public function setHttpFactory(HttpFactory $httpFactory): void;
}
