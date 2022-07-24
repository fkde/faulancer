<?php

namespace Faulancer\Event;

class TranslationMissingEvent extends AbstractEvent
{
    private string $translationKey;

    private array $translationData;

    /**
     * @param string $translationKey You could modify this variable to return a different key and other strings.
     * @param array $translationData
     */
    public function __construct(string &$translationKey, array $translationData)
    {
        parent::__construct($translationData);
        $this->translationKey = &$translationKey;
        $this->translationData = $translationData;
    }

    /**
     * @return string
     */
    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    /**
     * @param string $translationKey
     *
     * @return void
     */
    public function setTranslationKey(string $translationKey): void
    {
        $this->translationKey = $translationKey;
    }

    /**
     * @return array
     */
    public function getTranslationData(): array
    {
        return $this->translationData;
    }

}