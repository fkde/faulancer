<?php

namespace Faulancer\Command;

use PDO;
use ORM\Exception\NoConnection;
use Faulancer\Command\Input\Input;
use Faulancer\Command\Output\Output;
use Faulancer\Exception\ConsoleException;
use Faulancer\Migration\AbstractMigration;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use Faulancer\Service\Aware\EntityManagerAwareInterface;

class DatabaseMigrationCommand extends AbstractCommand implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    public const NAME = 'database:migrate';

    private int $executedMigrations = 0;

    protected function configure(): void
    {
        $this
            ->setTitle('Execute Migrations')
            ->setDescription('Execute migrations in either direction.')
            ->addArgument('direction')
            ->addOption('name', 'n')
            ->addOption('force', 'f');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     * @throws ConsoleException
     * @throws NoConnection
     */
    public function run(Input $input, Output $output): int
    {
        $direction = $input->getArgument('direction');

        if (false === \in_array($direction, ['up', 'down'])) {
            throw new ConsoleException('Invalid direction given.');
        }

        $this->ensureMigrationTrackingIntegrity($output);

        $em             = $this->getEntityManager();
        $connection     = $em->getConnection();
        $migrationsPath = __DIR__ . '/../../../src/Migration';
        $files          = array_diff(scandir($migrationsPath), ['.', '..']);

        $existingMigrations = $connection
            ->query('SELECT `name` from `migration`')
            ->fetchAll(\PDO::FETCH_COLUMN);

        // Override files with migration file from argument option when given
        if ($input->getOption('name')) {
            $name  = $input->getOption('name')[0] . '.php';
            $files = [$name];
        }

        foreach ($files as $file) {
            $className = substr($file, 0, -4);
            $namespace = sprintf('\App\Migration\%s', $className);

            if (
                ($direction !== 'down' && \in_array($className, $existingMigrations, true))
                || ($direction === 'down' && !\in_array($className, $existingMigrations, true))
                || false === class_exists($namespace)
            ) {
                continue;
            }

            $result = $this->executeMigration($namespace, $direction, $output);

            if (false === $result) {
                continue;
            }

            $output->writeLine(sprintf('✔ %s %s executed', $className, $direction));
        }

        if (0 === $this->executedMigrations) {
            $output->writeLine('No migrations executed.', 'warning');
        } else {
            $output->writeEmptyLine();
            $output->writeLine(
                sprintf(
                    '%d migration(s) successfully executed.',
                    $this->executedMigrations
                ),
                'ok'
            );
        }

        $isForce = $input->getOption('force') !== null;

        return self::SUCCESS;
    }

    /**
     * @param string $migration
     * @param string $direction
     * @param Output $output
     * @return bool
     *
     * @throws NoConnection
     */
    private function executeMigration(string $migration, string $direction, Output $output): bool
    {
        $connection = $this->getEntityManager()->getConnection();

        if (false === $connection instanceof PDO) {
            return false;
        }

        /** @var AbstractMigration $object */
        $object = new $migration();

        try {
            $object->$direction($connection);
        } catch (\PDOException $t) {
            $output->writeLine($t->getMessage() . ' in ' . $migration, 'error');
            throw $t;
        }


        ++$this->executedMigrations;

        $name = substr($migration, strrpos($migration, '\\') + 1);

        switch ($direction) {
            case 'up':
                $connection->exec("INSERT INTO `migration` (`name`) VALUES ('" . $name . "')");
                break;
            case 'down':
                $connection->exec("DELETE FROM `migration` WHERE `name`= '" . $name . "'");
        }

        return true;
    }

    /**
     * @throws NoConnection
     */
    private function ensureMigrationTrackingIntegrity(Output $output): void
    {
        $connection = $this->getEntityManager()->getConnection();

        $query  = $connection->query('SHOW TABLES');
        $result = $query->fetchAll(\PDO::FETCH_COLUMN);

        if (\in_array('migration', $result, true)) {
            return;
        }

        $output->writeLine('(!) Creating migration table as it doesn\'t exist.', 'warning');
        $output->writeEmptyLine();

        $connection->exec(<<<SQL
CREATE TABLE `migration`( 
    id INT(12) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(32) NOT NULL,
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
)
SQL);
    }
}
