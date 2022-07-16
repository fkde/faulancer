<?php

namespace Faulancer\Service\Aware;

use Psr\Http\Message\RequestInterface;

interface RequestAwareInterface
{

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request): void;

}