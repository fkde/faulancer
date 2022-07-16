<?php

namespace Faulancer\Service\Aware;

use Psr\Http\Message\RequestInterface;

/**
 * Trait RequestAwareTrait
 * @package Hardkode\Service
 */
trait RequestAwareTrait
{

    /** @var RequestInterface */
    private $request;

    /**
     * @return mixed
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     */
    public function setRequest(RequestInterface $request): void
    {
        $this->request = $request;
    }

}