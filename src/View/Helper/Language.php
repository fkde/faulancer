<?php

namespace Faulancer\View\Helper;

use Faulancer\Service\Aware\ConfigAwareInterface;
use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Value\Language as LangValueObject;

class Language extends AbstractViewHelper implements ConfigAwareInterface
{

    use ConfigAwareTrait;

    public function __invoke(): self
    {
        return $this;
    }

    /**
     * @param bool $asISO
     * @return string
     */
    public function getCurrentLanguage(bool $asISO = false):? string
    {
        $lang = $this->getConfig()->get('language');
        return (true === $asISO)
            ? LangValueObject::$isoCodes[$lang]
            : $lang;
    }

}