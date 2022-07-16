<?php

namespace Faulancer;

use Faulancer\Http\Client\Adapter\AdapterInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CurlAdapter implements AdapterInterface
{

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function request(RequestInterface $request): ResponseInterface
    {
        $uri = (string)$request->getUri();

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $uri);

        $response = curl_exec($ch);
        $factory  = new Psr17Factory();

        return $factory
            ->createResponse(200)
            ->withBody($factory->createStream($response));
    }
}
