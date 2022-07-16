<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\User;

interface UserAwareInterface
{

    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @param User $user
     * @return void
     */
    public function setUser(User $user): void;

}