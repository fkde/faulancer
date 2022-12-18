<?php

namespace Faulancer\Controller;

use \Throwable;
use Faulancer\Event\Observer;
use Faulancer\Service\Environment;
use Faulancer\Event\ExceptionEvent;
use Psr\Http\Message\StreamInterface;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Exception\FrameworkException;
use Faulancer\Exception\PermissionException;
use Faulancer\Exception\ViewHelperException;
use Faulancer\Exception\FileNotFoundException;
use Faulancer\Service\Aware\ObserverAwareTrait;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Aware\ObserverAwareInterface;

/**
 * Class ErrorController
 */
class ErrorController extends AbstractController implements ObserverAwareInterface
{
    use ObserverAwareTrait;

    private const TYPE_EXCEPTION = 'Exception';
    private const TYPE_ERROR = 'Error';

    /**
     * @param Throwable $t
     *
     * @return string
     */
    public function onException(Throwable $t): string
    {
        try {
            if (getenv('APPLICATION_ENV') === Environment::PRODUCTION) {
                $this->getObserver()->notify(new ExceptionEvent($t));
                return (string)$this->render('/error/404.phtml')->getBody();
            }
        } catch (\Throwable $t) {
            $t = new FrameworkException('Could not render error.', [], 500, $t);
        }

        return $this->renderStacktrace($t);
    }

    /**
     * @param int        $code
     * @param string     $message
     * @param string     $file
     * @param int        $line
     * @param array|null $context
     *
     * @return string
     */
    public function onError(int $code, string $message, string $file, int $line, ?array $context = []): string
    {
        $this->getLogger()->error(
            sprintf('%s in %s:%d', $message, $file, $line),
            ['file' => $file, 'line' => $line]
        );

        $exception = new FrameworkException($message, $context, $code);

        return $this->onException($exception);
    }

    /**
     * @param Throwable $t
     * @param string    $type
     *
     * @return string
     */
    private function renderStacktrace(Throwable $t, string $type = self::TYPE_EXCEPTION): string
    {
        $occurrence = sprintf('%s:%d', $t->getFile(), $t->getLine());
        $additionalOptions = null;

        if ($t instanceof FrameworkException) {
            $additionalOptions = join('<br />', $t->getContext()['additionalOptions'] ?? []);
        }

        return $this->output($t->getMessage(), $type, $occurrence, $t->getTraceAsString(), $additionalOptions);
    }

    /**
     * @param string $message
     * @param string $type
     * @param string $occurrence
     * @param string $trace
     * @param        $additionalOptions
     *
     * @return string
     */
    private function output(string $message, string $type, string $occurrence, string $trace, $additionalOptions = null): string
    {
        return <<<LAYOUT
<!DOCTYPE htmL>
<html lang="en">
<head>
    <title>{$type}</title>
    <style>
        html, body { margin: 0; padding: 0; width: 100%; height: auto; font-family: Verdana }
        .content { width: 60%; max-width: 768px; margin: 0 auto; line-height: 1.4rem;}
        .type {padding: 10px; box-sizing: border-box; background-color: darkred; color: white; font-size: .8rem}
        .title {padding: 40px 10px 0 10px; font-size: 1.6rem}
        .occurence {padding: 10px; font-size: 0.9rem}
        .additional {padding: 10px; font-style: italic}
        .trace {padding: 10px; white-space: pre; font-size: 0.7rem}
    </style>
</head>
<body>
    <div class="content">
        <div class="type">{$type}</div>
        <div class="title">{$message}</div>
        <div class="additional">{$additionalOptions}</div>
        <div class="occurence">in <strong>{$occurrence}</strong></div>
        <div class="trace">{$trace}</div>
    </div>
</body>
</html>
LAYOUT;
    }
}
