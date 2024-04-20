<?php

namespace Faulancer\Auth;

use Faulancer\Http\Client\HttpClient;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{

    /**
     * @param $clientIdentifier
     *
     * @return ClientEntity
     */
    public function getClientEntity($clientIdentifier): ClientEntityInterface
    {
        return new ClientEntity($clientIdentifier);
    }

    /**
     * @param $clientIdentifier
     * @param $clientSecret
     * @param $grantType
     *
     * @return bool
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        return true;
    }

}