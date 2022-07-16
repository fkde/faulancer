<?php

namespace Faulancer\Service\Aware;

use Faulancer\Database\EntityManager;

trait EntityManagerAwareTrait
{

    private EntityManager $entityManager;

    /**
     * @return mixed
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager): void
    {
        $this->entityManager = $entityManager;
    }

}