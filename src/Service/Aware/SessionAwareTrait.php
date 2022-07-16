<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\Session;

trait SessionAwareTrait
{

    private Session $session;

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
    }

}