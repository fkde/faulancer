<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\Translator;

trait TranslatorAwareTrait
{

    private Translator $translator;

    /**
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }
}
