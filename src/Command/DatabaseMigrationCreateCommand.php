<?php

namespace Faulancer\Command;

use Faulancer\Command\Input\Input;
use Faulancer\Command\Output\Output;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use Faulancer\Service\Aware\EntityManagerAwareInterface;

class DatabaseMigrationCreateCommand extends AbstractCommand implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    public const NAME = 'database:migration:create';


    protected function configure(): void
    {
        $this
            ->setTitle('Create Migration')
            ->setDescription('Create a skeleton migration in your application.')
            ->addOption('force', 'f');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     */
    public function run(Input $input, Output $output): int
    {
        $timestamp = time();
        $template  = $this->buildTemplate($timestamp);

        if (!is_dir('./../src/Migration')) {
            mkdir('./../src/Migration');
        }

        $migrationsDir = realpath('./../src/Migration');
        $filename      = sprintf('Migration%s.php', $timestamp);

        file_put_contents(sprintf('%s/%s', $migrationsDir, $filename), $template);

        $output->writeLine('Generated migration file successfully.', 'ok');

        return AbstractCommand::SUCCESS;
    }

    /**
     * @param int $timestamp
     * @return string
     */
    private function buildTemplate(int $timestamp): string
    {
        return <<<PHP
<?php

namespace App\Migration;

use \PDO;
use Faulancer\Database\Migration\AbstractMigration;

class Migration{$timestamp} extends AbstractMigration
{

    /**
     * @param PDO \$connection
     */
    public function up(PDO \$connection): void
    {
        
    }

    /**
     * @param PDO \$connection
     */
    public function down(PDO \$connection): void
    {
        
    }
}

PHP;
    }

}