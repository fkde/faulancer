<?php

namespace Faulancer\View\Helper;

use Faulancer\Exception\FrameworkException;
use Faulancer\Service\Aware\TranslatorAwareInterface;
use Faulancer\Service\Aware\TranslatorAwareTrait;

class Translate extends AbstractViewHelper implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * @param string $key
     * @param array  $variables
     *
     * @return string
     * @throws FrameworkException
     */
    public function __invoke(string $key, array $variables = []): string
    {
        return $this->getTranslator()->translate($key, $variables);
    }

}