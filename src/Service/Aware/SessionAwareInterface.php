<?php

namespace Faulancer\Service\Aware;

use Faulancer\Service\Session;

interface SessionAwareInterface
{

    /**
     * @return Session
     */
    public function getSession(): Session;

    /**
     * @param Session $session
     * @return void
     */
    public function setSession(Session $session): void;

}