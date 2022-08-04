<?php

namespace Faulancer\Database;

use ORM\DbConfig;
use Faulancer\Config;
use ORM\EntityManager as ORMEntityManager;

class EntityManager extends ORMEntityManager
{
    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $options = [
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ];
        }
        $dbConfig = $config->get('app:database');
        $dsn      = $dbConfig['dsn'] ?? null;
        $user     = $dbConfig['user'] ?? null;
        $pass     = $dbConfig['pass'] ?? null;
        $host     = $dbConfig['host'] ?? null;
        $db       = $dbConfig['db'] ?? null;
        $port     = $dbConfig['port'] ?? 3306;

        if (null !== $dsn) {

            $pdo = new \PDO($config->get('app:database:dsn'));

            $options = [
                ORMEntityManager::OPT_CONNECTION => $pdo
            ];
        } elseif (null !== $user && null !== $pass && null !== $host && null !== $db) {
            $options = [
                ORMEntityManager::OPT_CONNECTION => new DbConfig(
                    'mysql',
                    $db,
                    $user,
                    $pass,
                    $host,
                    $port
                )
            ];
        }

        $this->defineForNamespace('Faulancer');
        parent::__construct($options);
    }
}
