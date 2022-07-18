<?php

namespace Faulancer\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;

class FrameworkException extends \Exception
{

    private array $context;

    /**
     * @param string         $message
     * @param array          $context
     * @param int            $code
     * @param Throwable|null $previous
     */
    #[Pure] public function __construct(
        string $message = "",
        array $context = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * @return array|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }
}
