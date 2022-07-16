<?php

namespace Faulancer\Form\Validator;

use Faulancer\Form\AbstractValidator;

class Csrf extends AbstractValidator
{

    /**
     * @param $value
     * @return bool
     */
    public function exec($value): bool
    {
        return $value === $this->session->get('csrf_' . $this->field->getForm()->getId());
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return 'form.validator.csrf-token-is-invalid';
    }

}