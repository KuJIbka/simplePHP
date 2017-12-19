<?php

namespace Main\Service\Session\handlers;

use Main\Exception\CommonFatalError;

class SessionMemcachedHandler extends SessionHandlerAbstract
{
    /** @var \Memcached */
    protected $memcached;
    protected $prefix = '';
    protected $gcMaxLifeTime = 0;
    protected $maxLockTime = 0;

    public function __construct(
        \Memcached $memcached,
        int $gcMaxLifeTime = 1440,
        int $maxLockTime = 15,
        $prefix = 'PHPSESSID'
    ) {
        $this->memcached = $memcached;
        $this->prefix = $prefix;
        $this->gcMaxLifeTime = $gcMaxLifeTime;
        $this->maxLockTime = $maxLockTime;
    }

    /**
     * @param string $key
     * @return bool
     * @throws CommonFatalError
     */
    public function sessionLock(string $key): bool
    {
        $timeout = $this->maxLockTime * 1000000;
        $expireTimeout = $timeout / 500000;
        $lockSessionName = $this->getSessionLockName($key);
        $isSet = false;
        while (!$isSet && $timeout >= 0) {
            $isSet = $this->memcached->add(
                $lockSessionName,
                uniqid(),
                $_SERVER['REQUEST_TIME'] + $expireTimeout
            );
            if ($isSet) {
                return true;
            }
            $usleepVal = rand(100, 300);
            usleep($usleepVal * 1000);
            $timeout -= $usleepVal;
        }
        if (!$isSet && $timeout < 0) {
            throw new CommonFatalError();
        }
        return false;
    }

    public function sessionUnlock(string $key)
    {
        $this->memcached->delete($this->getSessionLockName($key));
    }

    public function getSessionLockName(String $key): string
    {
        return 'lock_'.$key;
    }

    public function getMemcached(): \Memcached
    {
        return $this->memcached;
    }

    private function getMemcachedKey(string $session_id): string
    {
        return $this->prefix.':'.$session_id;
    }

    protected function doRead(string  $sessionId): string
    {
        return $this->memcached->get($this->getMemcachedKey($sessionId)) ?: '';
    }

    protected function doWrite(string $session_id, string $session_data): bool
    {
        return $this->memcached->set(
            $this->getMemcachedKey($session_id),
            $session_data,
            $_SERVER['REQUEST_TIME'] + $this->gcMaxLifeTime
        );
    }

    protected function doDestroy(string $session_id): bool
    {
        $this->memcached->delete($this->getMemcachedKey($session_id));
        return true;
    }

    protected function doClose(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function updateTimestamp($key, $val)
    {
        return $this->memcached->touch($this->getMemcachedKey($key), $this->gcMaxLifeTime);
    }

    /** {@inheritdoc} */
    public function gc($maxlifetime)
    {
        return true;
    }
}
