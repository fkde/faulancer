<?php

namespace Faulancer\Form\Type;

use Assert\Assert;
use Faulancer\Form\AbstractType;

class Csrf extends AbstractType
{

    /**
     * @param array $definition
     * @param array $validators
     */
    public function __construct(array $definition, array $validators = [])
    {
        parent::__construct($definition, $validators);

        Assert::that($definition)->notEmptyKey('value');

        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        return sprintf(
            '<input type="hidden" name="csrf" data-id="%s" value="%s" />',
            $this->definition['data-id'] ?? null,
            $this->definition['value']
        );
    }

}