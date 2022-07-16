<?php

namespace Faulancer\Service;

use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Aware\ConfigAwareInterface;
use Faulancer\Service\Aware\LoggerAwareInterface;

class Translator implements ConfigAwareInterface, LoggerAwareInterface
{
    use ConfigAwareTrait;
    use LoggerAwareTrait;

    private string $country;

    private bool $isLanguageDetected;

    private array $translationData;

    /**
     * @param string $country
     */
    public function __construct(string $country = 'de')
    {
        $this->country = $country;
        $this->isLanguageDetected = false;
    }

    /**
     * @param string $translationKey
     * @param array  $variables
     *
     * @return string
     */
    public function translate(string $translationKey = 'none', array $variables = []): string
    {
        $this->detectCountry();

        $translationFilePath = sprintf(
            '%s%s.json',
            $this->getConfig()->get('app:translation:path'),
            $this->country);

        $data = json_decode(file_get_contents($translationFilePath), true);

        $translatedString = $data[$translationKey] ?? null;

        if (null === $translatedString) {
            return $translationKey;
        }

        if ($variables) {
            $keys = array_map(fn($item) => ('{{ ' . $item . ' }}'), array_keys($variables));
            $values = array_values($variables);
            $translatedString = str_replace($keys, $values, $translatedString);
        }

        return $translatedString;
    }

    /**
     * @return string|null
     */
    public function detectCountry():? string
    {
        if ($this->isLanguageDetected) {
            return $this->country;
        }

        $lang = $this->config->get('language') ?? $this->country;
        $this->country = $lang;
        $this->isLanguageDetected = true;

        $this->getLogger()->debug('Translator: Language "' . $lang . '" detected.');

        return $lang;
    }

}