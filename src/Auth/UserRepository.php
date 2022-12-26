<?php

namespace Faulancer\Auth;

use Faulancer\Entity\User;
use Faulancer\Entity\UserVault;
use Faulancer\Service\Aware\EntityManagerAwareInterface;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use ORM\Exception\IncompletePrimaryKey;
use ORM\Exception\NoEntity;

class UserRepository implements UserRepositoryInterface, EntityManagerAwareInterface
{

    use EntityManagerAwareTrait;

    /**
     * @param                       $username
     * @param                       $password
     * @param                       $grantType
     * @param ClientEntityInterface $clientEntity
     *
     * @return UserEntityInterface|User|null
     *
     * @throws IncompletePrimaryKey
     * @throws NoEntity
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity): UserEntityInterface|User|null
    {
        return $this->entityManager->fetch(UserVault::class)
            ->where('login_name', '=', $username)
            ->andWhere('hash', '=', $password)
            ->one();
    }

}