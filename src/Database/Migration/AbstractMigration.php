<?php

namespace Faulancer\Database\Migration;

use PDO;

abstract class AbstractMigration
{
    /**
     * @param PDO $connection
     */
    abstract public function up(PDO $connection): void;

    /**
     * @param PDO $connection
     */
    abstract public function down(PDO $connection): void;
}
