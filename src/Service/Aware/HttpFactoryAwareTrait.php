<?php

namespace Faulancer\Service\Aware;

use Faulancer\Http\HttpFactory;

trait HttpFactoryAwareTrait
{

    private HttpFactory $httpFactory;

    /**
     * @return HttpFactory
     */
    public function getHttpFactory(): HttpFactory
    {
        return $this->httpFactory;
    }

    /**
     * @param HttpFactory $httpFactory
     */
    public function setHttpFactory(HttpFactory $httpFactory): void
    {
        $this->httpFactory = $httpFactory;
    }
}
