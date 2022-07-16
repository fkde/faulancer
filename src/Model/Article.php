<?php

namespace Faulancer\Model;

use ORM\Entity;

/**
 * @property string   $title
 * @property string   $teaser
 * @property string   $content
 * @property User     $author
 * @property Category $category
 * @property string   $insertedAt
 * @property string   $updatedAt
 * @property string   $updatedBy
 */
class Article extends Entity
{

    protected static $tableName = 'article';

    protected static $relations = [
        'author'   => [User::class, ['userId' => 'id']],
        'comments' => [Comment::class, 'article'],
        'category' => [Category::class, ['categoryId' => 'id']]
    ];
}
