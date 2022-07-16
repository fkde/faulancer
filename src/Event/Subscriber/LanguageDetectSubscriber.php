<?php

namespace Faulancer\Event\Subscriber;

use Faulancer\Http\Response;
use Faulancer\Event\RequestEvent;
use Faulancer\Event\ConfigLoadedEvent;
use Faulancer\Event\AbstractSubscriber;
use Faulancer\Service\Aware\ConfigAwareTrait;
use Faulancer\Service\Aware\ConfigAwareInterface;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\LoggerAwareTrait;

class LanguageDetectSubscriber extends AbstractSubscriber implements ConfigAwareInterface, LoggerAwareInterface
{
    use ConfigAwareTrait;
    use LoggerAwareTrait;

    /**
     * @return string[]
     */
    public static function subscribe(): array
    {
        return [
            RequestEvent::class => 'onRequest'
        ];
    }

    /**
     * @param RequestEvent $event
     * @return void|null
     */
    public function onRequest(RequestEvent $event)
    {
        $validLanguages = ['de', 'en'];
        $browserLanguages = $event->getRequest()->getHeader('Accept-Language')[0] ?? null;

        if (null === $browserLanguages) {
            $this->getLogger()->debug('Browser language couldn\'t been detected.');
            return null;
        }

        foreach ($validLanguages as $language) {
            if (str_starts_with($browserLanguages, $language)) {
                $this->getConfig()->setLanguage($language);
                $this->getLogger()->debug('LanguageDetect: Detected language "' . $language . '".');
                break;
            }
        }
    }
}