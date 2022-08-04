<?php

namespace Faulancer\Service;

use Faulancer\Event\TranslationMissingEvent;
use Faulancer\Exception\FileNotFoundException;
use Faulancer\Exception\FrameworkException;
use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Service\Aware\LoggerAwareTrait;
use Faulancer\Service\Aware\ConfigAwareInterface;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\ObserverAwareInterface;
use Faulancer\Service\Aware\ObserverAwareTrait;

class Translator implements ConfigAwareInterface, LoggerAwareInterface, ObserverAwareInterface
{
    use ConfigAwareTrait;
    use LoggerAwareTrait;
    use ObserverAwareTrait;

    private ?string $country;

    private bool $isLanguageDetected;

    private ?array $translationData = null;

    /**
     * @param string|null $country
     */
    public function __construct(?string $country = null)
    {
        $this->country = $country;
        $this->isLanguageDetected = false;
    }

    /**
     * @param string $translationKey
     * @param array  $variables
     *
     * @return string
     *
     * @throws FrameworkException
     */
    public function translate(string $translationKey = 'none', array $variables = []): string
    {
        $this->detectCountry();

        if (null === $this->country) {
            throw new FrameworkException('Language identifier could not be found.', [
                'additionalOptions' => [
                    'Are the translation files correctly created?'
                ]
            ]);
        }

        if (null === $this->translationData) {
            $this->translationData = $this->loadTranslationFile($this->country);
        }

        $translatedString = $this->translationData[$translationKey] ?? null;

        if (null !== $translationKey && null === $translatedString) {
            $this->getObserver()->notify(new TranslationMissingEvent($translationKey, $this->translationData ?? []));
            return $translationKey;
        }

        if ($variables) {
            $keys = array_map(fn($item) => ('{{ ' . $item . ' }}'), array_keys($variables));
            $values = array_values($variables);
            $translatedString = str_replace($keys, $values, $translatedString);
        }

        return $translatedString;
    }

    private function loadTranslationFile(string $lang)
    {
        try {
            $translationFilePath = sprintf(
                '%s%s.json',
                $this->getConfig()->get('app:translation:path'),
                $this->country
            );

            return json_decode(file_get_contents($translationFilePath), true);
        } catch(\Throwable $e) {
            var_dump($e);
        }
    }

    /**
     * @return string|null
     */
    private function detectCountry():? string
    {
        if ($this->isLanguageDetected) {
            return $this->country;
        }

        $lang = $this->config->get('language') ?? $this->country;

        if (null === $lang) {
            $this->getLogger()->warning('Translator: Couldn\'t detect language.');;
        }

        $this->country = $lang;
        $this->isLanguageDetected = $lang !== null;

        $this->getLogger()->debug('Translator: Language "' . $lang . '" detected.');

        return $lang;
    }

}