<?php

namespace Faulancer\Controller;

use \Throwable;
use App\Controller\PageController;
use Psr\Http\Message\StreamInterface;
use Faulancer\Exception\NotFoundException;
use Faulancer\Exception\TemplateException;
use Faulancer\Exception\PermissionException;
use Faulancer\Exception\ViewHelperException;
use Faulancer\Exception\FileNotFoundException;

/**
 * Class ErrorController
 */
class ErrorController extends PageController
{

    private const TYPE_EXCEPTION = 'Exception';
    private const TYPE_ERROR = 'Error';

    /**
     * @param Throwable $t
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws Throwable
     * @throws ViewHelperException
     */
    public function onNotFound(Throwable $t): string
    {
        if (getenv('APPLICATION_ENV') === 'production') {
            return $this->render('/error/404.phtml')->getBody();
        }

        return $this->renderStacktrace($t);
    }

    /**
     * @param Throwable $t
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws Throwable
     * @throws ViewHelperException
     */
    public function onException(\Throwable $t): string
    {
        if ($t instanceof PermissionException) {
            $this->getLogger()->error($t->getMessage());
            return $this->render('/error/403.phtml')->getBody();
        }

        if ($t instanceof NotFoundException) {
            return $this->onNotFound($t);
        }

        $this->renderStacktrace($t);
    }

    /**
     * @param int        $code
     * @param string     $message
     * @param string     $file
     * @param int        $line
     * @param array|null $context
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws NotFoundException
     * @throws TemplateException
     * @throws Throwable
     * @throws ViewHelperException
     */
    public function onError(int $code, string $message, string $file, int $line, ?array $context = null): string
    {
        if (getenv('APPLICATION_ENV') === 'production') {
            return $this->render('/error/404.phtml')->getBody();
        }

        $err = new \Error($message, $code);
        return $this->renderStacktrace($err, self::TYPE_ERROR);
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

        return <<<EXCEPTION
<h2>{$type}</h2>
<h3>{$t->getMessage()}</h3>
<h5>{$occurrence}</h5>
<pre>{$t->getTraceAsString()}</pre>
EXCEPTION;
    }
}
