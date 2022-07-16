<?php

namespace Faulancer\Form\Type;

use Assert\Assert;
use Faulancer\Form\AbstractType;

class Input extends AbstractType
{

    /**
     * @param array $definition
     * @param array $validators
     */
    public function __construct(array $definition, array $validators = [])
    {
        parent::__construct($definition, $validators);

        Assert::that($definition)->notEmptyKey(
            'type',
            'You have to define the input type (e.g. text, date, number, etc.).'
        );

        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $result = '<input';

        if (!empty($this->getValue())) {
            $this->definition['value'] = $this->getValue();
        }

        foreach ($this->definition as $attribute => $value) {
            if ('label' === $attribute) {
                continue;
            }

            $result .= sprintf(' %s="%s"', $attribute, $this->getTranslator()->translate($value));
        }

        $result .= ' />';

        return $result;
    }

}