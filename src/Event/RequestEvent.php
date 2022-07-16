<?php

namespace Faulancer\Event;

use Faulancer\Exception\EventException;
use Psr\Http\Message\RequestInterface;

class RequestEvent extends AbstractEvent
{
    public const NAME = 'request.created';

    private RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        parent::__construct($request);
        $this->request = $request;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

}