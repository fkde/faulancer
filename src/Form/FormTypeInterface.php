<?php

namespace Faulancer\Form;

interface FormTypeInterface
{

    public function setForm(FormBuilderInterface $form);

    public function getForm(): FormBuilderInterface;

    public function setValue(string $value);

    public function getValue(): ?string;

    public function isValid(): bool;

    public function getErrorMessages(): string;

    public function render(): string;

}