<?php

namespace Faulancer\Http;

class JsonResponse extends Response
{
    public function __construct(int $status = 200, array $headers = [], $body = null, string $version = '1.1', string $reason = null)
    {
        $headers['Content-Type'] = 'application/json';
        parent::__construct($status, $headers, $body, $version, $reason);
    }
}