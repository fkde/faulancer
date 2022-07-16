<?php

namespace Faulancer\Form\Validator;

use Faulancer\Form\AbstractValidator;

class NotEmpty extends AbstractValidator
{

    /**
     * @param $value
     * @return bool
     */
    public function exec($value): bool
    {
        return !empty($value);
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return 'form.validator.field-is-empty';
    }

}