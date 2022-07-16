<?php

namespace Faulancer\Service;

use ORM\Exception\NoEntity;
use Faulancer\Model\User as UserModel;
use ORM\Exception\IncompletePrimaryKey;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Aware\SessionAwareTrait;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\SessionAwareInterface;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use Faulancer\Service\Aware\EntityManagerAwareInterface;

class User implements EntityManagerAwareInterface, SessionAwareInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    use EntityManagerAwareTrait;
    use SessionAwareTrait;

    /**
     * @return UserModel|null
     */
    public function getCurrentUser(): UserModel|null
    {
        try {
            return $this->getEntityManager()->fetch(
                UserModel::class,
                $this->getSession()->get('userId')
            );
        } catch (IncompletePrimaryKey | NoEntity $e) {
            $this->getLogger()->info($e->getMessage());
            return null;
        }
    }
}
