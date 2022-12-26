<?php

namespace Faulancer;

use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use ORM\Entity;
use Lcobucci\JWT\Token;
use Faulancer\Auth\UserRepository;
use Faulancer\Auth\RefreshTokenRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\PasswordGrant;

class Authenticator
{

    private AuthorizationServer $authorizationServer;

    /**
     * @param AuthorizationServer $authorizationServer
     */
    public function __construct(AuthorizationServer $authorizationServer)
    {
        $this->authorizationServer = $authorizationServer;
    }

    /**
     * @param Entity $user
     *
     * @return void
     */
    public function loginUser(Entity $user): Token
    {
        $userRepository = new UserRepository();
        $refreshTokenRepository = new RefreshTokenRepository();


        $grant = new PasswordGrant($userRepository, $refreshTokenRepository);

        $this->authorizationServer->enableGrantType(new ClientCredentialsGrant());
    }

}