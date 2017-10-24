<?php

namespace Main\Service\Session\handlers;

interface MainSessionHandlerInterface extends \SessionHandlerInterface
{
    public function sessionLock(string $key): bool;

    public function sessionUnlock(string $key);

    public function getSessionLockLockName(String $key): string;
}
