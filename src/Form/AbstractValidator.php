<?php

namespace Faulancer\Form;

use Faulancer\Service\Session;

abstract class AbstractValidator implements FormValidatorInterface
{
    protected FormTypeInterface $field;

    protected Session $session;

    /**
     * @param AbstractType $field
     * @param Session      $session
     */
    public function __construct(AbstractType $field, Session $session)
    {
        $this->field = $field;
        $this->session = $session;
    }

    /**
     * @return string
     */
    abstract public function getErrorMessage(): string;

    /**
     * @param $value
     * @return bool
     */
    abstract public function exec($value): bool;

}
