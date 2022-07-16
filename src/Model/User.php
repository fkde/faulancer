<?php

namespace Faulancer\Model;

use ORM\Entity;

/**
 * @property int       $id
 * @property string    $name
 * @property string    $email
 * @property string    $password
 * @property Role[]    $roles
 * @property Article[] $articles
 */
class User extends Entity
{

    protected static $tableName = 'user';

    protected static $relations = [
        'articles' => [Comment::class, 'article'],
        'roles' => [Role::class, ['id' => 'user_id'], 'users', 'user_role']
    ];
}
