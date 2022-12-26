<?php

namespace Faulancer\Entity;

use ORM\Entity;

class UserVault extends Entity
{

    protected static $relations = [
        'user' => [User::class, ['userId' => 'id']]
    ];

}