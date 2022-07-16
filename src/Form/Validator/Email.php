<?php

namespace Faulancer\Form\Validator;

use Faulancer\Form\AbstractValidator;

class Email extends AbstractValidator
{
    public function getErrorMessage(): string
    {
        return 'form.validator.email-is-invalid';
    }

    public function exec($value): bool
    {
        return !!filter_var($value, FILTER_VALIDATE_EMAIL);
    }


}