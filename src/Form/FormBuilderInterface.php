<?php

namespace Faulancer\Form;

use Psr\Http\Message\RequestInterface;

interface FormBuilderInterface
{

    public function open(): string;

    public function close(): string;

    public function isSubmitted(): bool;

    public function isValid(): bool;

    public function getData();

    public function create(RequestInterface $request);

}
