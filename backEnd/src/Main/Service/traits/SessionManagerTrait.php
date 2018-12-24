<?php

namespace Main\Service\traits;

use Main\Service\Session\SessionManager;

trait SessionManagerTrait
{
    /** @var SessionManager */
    protected $sessionManager;

    public function setSessionManager(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }
}
