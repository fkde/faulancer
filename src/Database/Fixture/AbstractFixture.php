<?php

namespace Faulancer\Database\Fixture;

use Faulancer\Database\EntityManager;

abstract class AbstractFixture
{
    /**
     * @param EntityManager $entityManager
     */
    abstract public function load(EntityManager $entityManager): void;
}
