<?php

namespace Faulancer\Auth;

use Faulancer\Entity\AccessToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use ORM\Exception\IncompletePrimaryKey;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{

    /**
     * @param ClientEntityInterface $clientEntity
     * @param array                 $scopes
     * @param                       $userIdentifier
     *
     * @return AccessToken
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null): AccessToken
    {
        $accessToken = new \Faulancer\Entity\AccessToken();
        $accessToken->setClient($clientEntity);
        foreach ($scopes as $scope) {
            $accessToken->addScope($scope);
        }
        $accessToken->setUserIdentifier($userIdentifier);

        return $accessToken;
    }

    /**
     * @param AccessTokenEntityInterface $accessTokenEntity
     *
     * @return void
     * @throws IncompletePrimaryKey
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $accessTokenEntity->save();
    }

    public function revokeAccessToken($tokenId)
    {
        // TODO: Implement revokeAccessToken() method.
    }

    public function isAccessTokenRevoked($tokenId): bool
    {
        return false;
    }

}