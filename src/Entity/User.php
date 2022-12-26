<?php

namespace Faulancer\Entity;

use League\OAuth2\Server\Entities\UserEntityInterface;
use ORM\Entity;

/**
 * @property int    $id
 * @property string $login_name
 * @property string $hash
 */
class User extends Entity implements UserEntityInterface
{

    protected static $tableName = 'user';

    protected static $relations = [
        'roles' => [Role::class, ['id' => 'user_id'], 'users', 'user_role'],
        'vault' => ['one', UserVault::class, 'user']
    ];

    /**
     * @return int
     */
    public function getIdentifier(): int
    {
        return $this->id ?? uniqid();
    }

}
