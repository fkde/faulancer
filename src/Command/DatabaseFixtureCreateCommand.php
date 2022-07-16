<?php

namespace Faulancer\Command;

use Faulancer\Command\Input\Input;
use Faulancer\Command\Output\Output;
use Faulancer\Exception\ConsoleException;
use Faulancer\Service\Aware\EntityManagerAwareInterface;
use Faulancer\Service\Aware\EntityManagerAwareTrait;

class DatabaseFixtureCreateCommand extends AbstractCommand implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    public const NAME = 'database:fixture:create';

    protected function configure(): void
    {
        $this
            ->setTitle('Create Fixture')
            ->addArgument('type')
            ->setDescription('Create a skeleton fixture in your application.')
            ->addOption('force', 'f');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     *
     * @throws ConsoleException
     */
    public function run(Input $input, Output $output): int
    {
        $type     = ucfirst($input->getArgument('type'));
        $template = $this->buildTemplate($type);

        $migrationsDir = __DIR__ . '/../../../src/Fixture';
        $filename      = sprintf('%sFixture.php', $type);
        $path          = sprintf('%s/%s', $migrationsDir, $filename);

        if (file_exists($path)) {
            throw new ConsoleException(
                sprintf(
                    'There already exists a fixture named %s',
                    $type
                )
            );
        }

        file_put_contents($path, $template);

        $output->writeLine('Generated fixture file successfully.', 'ok');

        return AbstractCommand::SUCCESS;
    }

    /**
     * @param string $type
     * @return string
     */
    private function buildTemplate(string $type): string
    {
        return <<<PHP
<?php

namespace App\Fixture;

use Faulancer\EntityManager;
use Faulancer\Fixture\AbstractFixture;

class {$type}Fixture extends AbstractFixture
{

    /**
     * @param EntityManager \$entityManager
     */
    public function load(EntityManager \$entityManager): void
    {
        
    }
}

PHP;
    }

}
