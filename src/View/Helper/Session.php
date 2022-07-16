<?php

namespace Faulancer\View\Helper;

use Faulancer\Service\Session as SessionService;

class Session extends AbstractViewHelper
{
    /**
     * @return SessionService
     */
    public function __invoke(): SessionService
    {
        return $this->getSession();
    }
}
