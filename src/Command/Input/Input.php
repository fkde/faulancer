<?php

namespace Faulancer\Command\Input;

use Faulancer\Exception\ConsoleException;

class Input
{

    private array $definition;
    private array $arguments = [];
    private array $options = [];

    /**
     * @param array $parameters
     * @param array $definition
     * @throws ConsoleException
     */
    public function __construct(array $parameters, array $definition)
    {
        $this->definition = $definition;
        $this->parse($parameters);
    }

    /**
     * @param array $parameters
     * @throws ConsoleException
     */
    private function parse(array $parameters): void
    {
        $options   = [];
        $arguments = [];

        foreach ($parameters as $index => $parameter) {
            if (true === str_starts_with($parameter, '--')) {
                // long option
                $raw = substr($parameter, 2);
                $parts = explode('=', $raw);

                $options[$parts[0]][] = $parts[1] ?? null;
                continue;
            }

            if (true === str_starts_with($parameter, '-')) {
                // short option
                $name = substr($parameter, 1);
                $options[$name][] = array_slice($parameters, $index + 1, 1)[0] ?? null;
                continue;
            }

            $previousItem = $parameters[$index - 1] ?? '';

            if (false === str_starts_with($previousItem, '-')) {
                $arguments[] = $parameter;
            }

        }

        $definitionArgs       = $this->definition['arguments'] ?? [];
        $isSameArgumentsCount = count($arguments) === count($definitionArgs);

        if (false === $isSameArgumentsCount && count($definitionArgs) > count($arguments)) {
            throw new ConsoleException(
                'You are expecting a argument which isn\'t given' . PHP_EOL
            );
        } elseif (false === $isSameArgumentsCount && count($definitionArgs) < count($arguments)) {
            throw new ConsoleException(
                'You have non configured arguments given' . PHP_EOL
            );
        }

        if ($isSameArgumentsCount) {
            $this->arguments = array_combine(array_flip($definitionArgs), $arguments);
        }

        // Add options (always optional)
        $definitionOpts = $this->definition['options'] ?? [];
        $optionsDelta   = array_diff(array_keys($options), $definitionOpts);
        $this->options  = array_combine($optionsDelta, $options);
    }

    public function getArgument(string $name): ?string
    {
        return $this->arguments[$name] ?? null;
    }

    public function getOption(string $name): string|array|null
    {
        return $this->options[$name] ?? null;
    }

    public function ask(string $message)
    {
        $input = readlink($message);
        if ($input) {
            readline_add_history($input);
        }
        return $input;
    }

}