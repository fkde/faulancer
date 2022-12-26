<?php

namespace Faulancer\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;

class ClientEntity implements ClientEntityInterface
{

    private string $clientIdentifier;

    /**
     * @param string $clientIdentifier
     */
    public function __construct(string $clientIdentifier)
    {
        $this->clientIdentifier = $clientIdentifier;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'lf';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'LoveFox';
    }

    /**
     * @return string
     */
    public function getRedirectUri(): string
    {
        return '/loginer';
    }

    public function isConfidential(): bool
    {
        return false;
    }
}