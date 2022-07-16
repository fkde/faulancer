<?php

namespace Faulancer\Service;

class Session
{

    private string $sessionId;

    public function __construct()
    {
        if (empty(session_id()) && session_start()) {
            $this->sessionId = session_id();
        }
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @param string $name
     * @return string|array|null
     */
    public function get(string $name): string|array|null
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     * @param string       $name
     * @param string|array $value
     */
    public function set(string $name, string|array $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function delete(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * @param string $name
     * @return string|array|null
     */
    public function getFlashMessage(string $name): string|array|null
    {
        $value = $_SESSION['flashbag'][$name] ?? null;

        if (null !== $value) {
            unset($_SESSION['flashbag'][$name]);
        }

        return $value;
    }

    /**
     * @param string $name
     * @param string|array $value
     */
    public function addFlashMessage(string $name, string|array $value): void
    {
        $_SESSION['flashbag'][$name] = $value;
    }

}