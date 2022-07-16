<?php

namespace Faulancer\Model;

use ORM\Entity;

/**
 * @property int    $id
 * @property int    $articleId
 * @property int    $userId
 * @property string $content
 */
class Comment extends Entity
{

    protected static $tableName = 'comment';

    protected static $relations = [
        'author'  => [User::class, ['userId' => 'id']],
        'article' => [Article::class, ['articleId' => 'id']]
    ];
}
