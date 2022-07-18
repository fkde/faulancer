<?php

namespace Faulancer\Event;

use Faulancer\Exception\EventException;
use Psr\Http\Message\RequestInterface;

class RequestEvent extends AbstractEvent
{
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

    public static function getName(): string
    {
        return 'request.created';
    }

}