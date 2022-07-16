<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\Translator;

interface TranslatorAwareInterface
{

    /**
     * @return Translator
     */
    public function getTranslator(): Translator;

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator): void;

}