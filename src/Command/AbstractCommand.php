<?php

namespace Faulancer\Command;

use Faulancer\Command\Input\Input;
use Faulancer\Command\Output\Output;
use Faulancer\Exception\ConsoleException;

abstract class AbstractCommand
{

    public const NAME = null;

    protected const SUCCESS = 0;
    protected const ERROR   = 1;

    private string $title = '';
    private string $description = '';

    private array $arguments = [];
    private array $options = [];

    public function __construct()
    {
        $this->configure();
    }

    /**
     * @return array
     */
    public function getDefinition(): array
    {
        return [
            'arguments' => $this->arguments,
            'options'   => $this->options
        ];
    }

    /**
     * @param $name
     * @return $this
     */
    protected function addArgument($name): self
    {
        $this->arguments[$name] = '';
        return $this;
    }

    /**
     * @param string $name
     * @param string|null $shortcut
     * @return $this
     */
    protected function addOption(string $name, ?string $shortcut = ''): self
    {
        $this->options[$name] = '';
        return $this;
    }

    /**
     * @param string $title
     * @return $this
     */
    protected function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $description
     * @return $this
     */
    protected function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    abstract protected function configure(): void;

    abstract public function run(Input $input, Output $output): int;

}