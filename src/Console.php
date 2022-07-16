<?php

namespace Faulancer;

use Faulancer\Command\AbstractCommand;
use Faulancer\Command\ListCommand;
use Faulancer\Exception\ConsoleException;
use Faulancer\Exception\NotFoundException;
use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Service\Aware\EntityManagerAwareTrait;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Psr\Container\ContainerInterface;

class Console
{
    use ConfigAwareTrait;
    use LoggerAwareTrait;
    use EntityManagerAwareTrait;

    private ContainerInterface $container;

    private array $commands;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function handle($parameters): void
    {
        try {
            $this->registerCoreCommands();
            $this->registerCustomCommands();

            $this->run($parameters);
        } catch (\Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    /**
     * @param array $parameters
     * @throws ConsoleException
     * @throws NotFoundException
     */
    private function run(array $parameters): int
    {
        // First argument is the bin/console command itself
        array_shift($parameters);

        $commandName = array_shift($parameters);
        $commandArgs = [];

        if (empty($commandName)) {
            //return $this->renderListCommand();
            //throw new ConsoleException('No command given');
            $commandName = 'command:list';
            $commandArgs[] = [array_values($this->commands)];
        }

        if (false === isset($this->commands[$commandName])) {
            throw new ConsoleException(
                sprintf('Command %s not found', $commandName)
            );
        }

        $command = $this->commands[$commandName];

        try {
            /** @var AbstractCommand $object */
            $object = Initializer::load($command, ...$commandArgs);
            $definition = $object->getDefinition();

            $input = new Command\Input\Input($parameters, $definition);
            $output = new Command\Output\Output();

            if ($object->getTitle()) {
                $output->drawStroke(strlen($object->getTitle()) + 3, '-');
                $output->writeLine(sprintf(' %s', $object->getTitle()), 'title');
                $output->drawStroke(strlen($object->getTitle()) + 3, '-');
                $output->writeEmptyLine();
            }

            $object->run($input, $output);

        } catch (ConsoleException $e) {
            $output->writeLine($e->getMessage(), 'error');
            print PHP_EOL;
            return 1;
        }

        print PHP_EOL;
        return 0;
    }

    /**
     * @param string|null $path
     * @return void
     */
    private function registerCoreCommands(?string $path = null): void
    {
        $coreCommandsPath = __DIR__ . '/Command';
        $namespacePath    = __NAMESPACE__ . '\Command';

        /** @var AbstractCommand[] $commands */
        $commands = $this->loadCommandsFromDirectory($coreCommandsPath, $namespacePath);

        foreach ($commands as $command) {
            $this->commands[$command::NAME] = $command;
        }
    }

    private function registerCustomCommands(): void
    {
        $customCommands = $this->getConfig()->get('commands');

        if (null === $customCommands) {
            return;
        }

        /** @var AbstractCommand[] $commands */
        foreach ($customCommands as $command) {
            $this->commands[$command::NAME] = $command;
        }
    }

    /**
     * @param string $directory
     * @param string $namespacePath
     * @return array
     */
    private function loadCommandsFromDirectory(string $directory, string $namespacePath): array
    {
        $contents = array_diff(scandir($directory), ['.', '..', 'AbstractCommand.php']);

        $files = array_filter(
            $contents,
            static function ($file) use ($directory) {
                return !is_dir($directory . '/' . $file);
            }
        );

        return array_map(static function ($command) use ($namespacePath) {
            $className = substr($command, 0, -4);
            return sprintf('\%s\%s', $namespacePath, $className);
        }, $files);
    }

    private function renderListCommand(): int
    {
        /** @var AbstractCommand $listCommand */
        $listCommand = Initializer::load(ListCommand::class);
        $input = new Command\Input\Input([], []);
        $output = new Command\Output\Output();

        $listCommand->run($input, $output);

        return 0;
    }
}
