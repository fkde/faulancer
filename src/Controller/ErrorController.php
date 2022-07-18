<?php

namespace Faulancer\Controller;

use Faulancer\Exception\FrameworkException;
use Faulancer\Service\Aware\LoggerAwareInterface;
use Faulancer\Service\Environment;
use \Throwable;
use Psr\Http\Message\StreamInterface;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Exception\PermissionException;
use Faulancer\Exception\ViewHelperException;
use Faulancer\Exception\FileNotFoundException;

/**
 * Class ErrorController
 */
class ErrorController extends AbstractController
{

    private const TYPE_EXCEPTION = 'Exception';
    private const TYPE_ERROR = 'Error';

    /**
     * @param Throwable $t
     *
     * @return void
     *
     * @throws FileNotFoundException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws Throwable
     * @throws ViewHelperException
     */
    public function onException(Throwable $t): void
    {
        if (getenv('APPLICATION_ENV') === Environment::PRODUCTION) {
            echo $this->render('/error/404.phtml')->getBody();
            return;
        }

        echo $this->renderStacktrace($t);
    }

    /**
     * @param int        $code
     * @param string     $message
     * @param string     $file
     * @param int        $line
     * @param array|null $context
     *
     * @return void
     */
    public function onError(int $code, string $message, string $file, int $line, ?array $context = null): void
    {
        $this->getLogger()->error($message, ['file' => $file, 'line' => $line]);

        if (getenv('APPLICATION_ENV') === Environment::PRODUCTION) {
            echo $this->render('/error/404.phtml')->getBody();
            return;
        }

        $err = new \ErrorException($message, $code, 1, $file, $line);
        echo $this->renderStacktrace($err, self::TYPE_ERROR);
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
        $layout = <<<LAYOUT
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

        return $layout;
    }
}
