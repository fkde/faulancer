<?php

namespace Faulancer\Entity;

use ORM\Entity;

/**
 * @property int    $id
 * @property string $name
 * @property User[] $users
 */
class Role extends Entity
{

    protected static $tableName = 'role';

    protected static $relations = [
        'users' => [User::class, ['id' => 'role_id'], 'roles', 'user_role']
    ];
}
