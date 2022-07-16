<?php

namespace Faulancer\Form\Type;

use Assert\Assert;
use Faulancer\Form\AbstractType;

class Checkbox extends AbstractType
{

    /**
    * @param array $definition
    * @param array $validators
    */
    public function __construct(array $definition, array $validators = [])
    {
        parent::__construct($definition, $validators);

        $definition['type'] = 'checkbox';
        $this->definition = $definition;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $result = '<input';
        $this->setValue(true);

        try {
            if (array_key_exists($this->getName(), $this->getForm()->getData())) {
                $result .= ' checked="checked"';
            }
        } catch (\Error $e) {
            // Expected error because `$this->getForm()` want to access a possible uninitialized typed property
        }

        if (empty($this->definition['id'])) {
            $result .= ' id="' . $this->getUniqueId() . '"';
        }

        foreach ($this->definition as $attribute => $value) {
            if ('label' === $attribute) {
                continue;
            }

            $result .= sprintf(' %s="%s"', $attribute, $value);
        }

        $result .= ' />';

        return $result;
    }
}
