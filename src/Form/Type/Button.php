<?php

namespace Faulancer\Form\Type;

use Assert\Assert;
use Faulancer\Exception\FrameworkException;
use Faulancer\Form\AbstractType;

class Button extends AbstractType
{

    /**
     * @param array $definition
     */
    public function __construct(array $definition)
    {
        parent::__construct($definition);
        Assert::that($definition)->notEmptyKey('text', 'You have to define a button text.');
        $this->definition = $definition;
    }

    /**
     * @return string
     * @throws FrameworkException
     */
    public function render(): string
    {
        $attributes = [];

        foreach ($this->definition as $attribute => $value) {
            if ($attribute === 'text') {
                continue;
            }
            $attributes[] = sprintf('%s="%s"', $attribute, $this->getTranslator()->translate($value));
        }

        $result = '<button ';
        $result .= implode(' ', $attributes);
        $result .= '>';
        $result .= $this->getTranslator()->translate($this->definition['text']);
        $result .= '</button>';

        return $result;
    }
}
