<?php

namespace Main\Service\Session\handlers;

use Main\Exception\CommonFatalError;

class SessionMemcacheHandler extends SessionHandlerAbstract
{
    /** @var \Memcache */
    protected $memcache;
    protected $prefix = '';
    protected $gcMaxLifeTime = 0;
    protected $maxLockTime = 0;

    public function __construct(
        \Memcache $memcache,
        int $gcMaxLifeTime = 1440,
        int $maxLockTime = 15,
        $prefix = 'PHPSESSID'
    ) {
        $this->memcache = $memcache;
        $this->prefix = $prefix;
        $this->gcMaxLifeTime = $gcMaxLifeTime;
        $this->maxLockTime = $maxLockTime;
    }

    public function doClose(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function doDestroy(string $session_id): bool
    {
        $this->memcache->delete($this->getMemcacheKey($session_id));
        return true;
    }

    /** {@inheritdoc} */
    public function gc($maxlifetime)
    {
        return true;
    }

    /** {@inheritdoc} */
    public function doRead(string $session_id): string
    {
        return $this->memcache->get($this->getMemcacheKey($session_id)) ?: '';
    }

    /** {@inheritdoc} */
    public function doWrite(string $session_id, string $session_data): bool
    {
        return $this->memcache->set($this->getMemcacheKey($session_id), $session_data, 0, $this->gcMaxLifeTime);
    }

    public function sessionLock(string $key): bool
    {
        $timeout = $this->maxLockTime * 1000000;
        $expireTimeout = $timeout / 500000;
        $lockSessionName = $this->getSessionLockName($key);
        $isSet = false;
        while (!$isSet && $timeout >= 0) {
            $isSet = $this->memcache->add($lockSessionName, uniqid(), 0, $expireTimeout);
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
        $this->memcache->delete($this->getSessionLockName($key));
    }

    public function getSessionLockName(String $key): string
    {
        return 'lock_'.$key;
    }

    public function getMemcache(): \Memcache
    {
        return $this->memcache;
    }

    private function getMemcacheKey(string $session_id): string
    {
        return $this->prefix.':'.$session_id;
    }

    /** {@inheritdoc} */
    public function updateTimestamp($key, $val)
    {
        return true;
    }
}
