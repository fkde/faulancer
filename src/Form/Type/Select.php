<?php

namespace Faulancer\Form\Type;

use Assert\Assert;
use Faulancer\Form\AbstractType;

class Select extends AbstractType
{
    public function __construct(array $definition, array $validators = [])
    {
        Assert::that($definition)->keyExists('options');
        Assert::that($definition)->notEmpty();
        parent::__construct($definition, $validators);
    }

    public function render(): string
    {
        $name = $this->definition['name'];
        $options = $this->definition['options'];

        $dom = new \DOMDocument();

        $select = $dom->createElement('select');
        $select->setAttribute('name', $name);

        foreach ($options as $optionKey => $optionValue) {
            $node = $dom->createElement('option');
            $node->setAttribute('value', $optionKey);
            $node->textContent = $optionValue;
            $select->appendChild($node);
        }

        $dom->appendChild($select);

        return $dom->saveHTML();
    }

}
