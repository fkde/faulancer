<?php

namespace Faulancer\Model;

use ORM\Entity;

/**
 * @property int    $id
 * @property string $name
 * @property string $insertedAt;
 */
class Category extends Entity
{

    protected static $tableName = 'article';

    protected static $relations = [
        'author'   => [User::class, ['userId' => 'id']],
        'articles' => [Article::class, 'category']
    ];
}
