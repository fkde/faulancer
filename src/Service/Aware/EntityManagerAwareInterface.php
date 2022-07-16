<?php

namespace Faulancer\Service\Aware;

use Faulancer\Database\EntityManager;

interface EntityManagerAwareInterface
{

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager): void;

}