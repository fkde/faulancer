<?php

namespace Faulancer\Form;

interface FormValidatorInterface
{

    public function getErrorMessage(): string;

    public function exec($value): bool;

}