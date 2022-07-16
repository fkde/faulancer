<?php

namespace Faulancer\Command;

use Faulancer\Initializer;
use Faulancer\Command\Input\Input;
use Faulancer\Command\Output\Output;
use Faulancer\Fixture\AbstractFixture;
use Faulancer\Exception\NotFoundException;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use Faulancer\Service\Aware\EntityManagerAwareInterface;

class DatabaseFixtureExecuteCommand extends AbstractCommand implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    public const NAME = 'database:fixtures:load';

    protected function configure(): void
    {
        $this
            ->setTitle('Execute Fixtures')
            ->setDescription('Create a skeleton fixture in your application.')
            ->addOption('force', 'f');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return int
     *
     * @throws NotFoundException
     */
    public function run(Input $input, Output $output): int
    {
        $fixturesDir = __DIR__ . '/../../../src/Fixture';

        $fixtures = array_diff(scandir($fixturesDir), ['.', '..']);
        $count = 0;
        $output->writeLine('Executing fixtures...');

        foreach ($fixtures as $filename) {
            $className = substr($filename, 0, -4);
            $class = sprintf('\App\Fixture\%s', $className);

            /** @var AbstractFixture $fixture */
            $fixture = Initializer::load($class);
            $fixture->load($this->getEntityManager());

            $output->writeLine($class);
            ++$count;
        }

        $output->writeEmptyLine();
        $output->writeLine(
            sprintf('Executed %d fixtures successfully.', $count),
            'ok'
        );

        return AbstractCommand::SUCCESS;
    }
}
