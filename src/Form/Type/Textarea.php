<?php

namespace Faulancer\Form\Type;

use Faulancer\Form\AbstractType;

class Textarea extends AbstractType
{
    /**
     * @return string
     */
    public function render(): string
    {
        $textareaValue = null;
        $result        = '<textarea';

        if (!empty($this->getValue())) {
            $this->definition['value'] = $this->getValue();
        }

        foreach ($this->definition as $attribute => $value) {
            if ($attribute === 'value') {
                $textareaValue = $value;
                continue;
            }

            $result .= sprintf(' %s="%s"', $attribute, $this->getTranslator()->translate($value));
        }

        $result .= '>' . $textareaValue . '</textarea>';

        return $result;
    }
}
