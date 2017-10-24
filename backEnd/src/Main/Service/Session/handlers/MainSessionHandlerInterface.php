<?php

namespace Main\Service\Session\handlers;

interface MainSessionHandlerInterface extends \SessionHandlerInterface
{
    public function sessionLock(string $key);

    public function sessionUnlock(string $key);

    public function getSessionLockKeyName(String $key): string;
}
