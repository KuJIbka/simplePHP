<?php

namespace Main\Service\Session\handlers;

use Main\Exception\CommonFatalError;

class SessionMemcachedHandler implements MainSessionHandlerInterface
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

    /** {@inheritdoc} */
    public function close()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function destroy($session_id)
    {
        $this->memcached->delete($this->getMemcachedKey($session_id));
    }

    /** {@inheritdoc} */
    public function gc($maxlifetime)
    {
        return true;
    }

    /** {@inheritdoc} */
    public function open($save_path, $name)
    {
        return true;
    }

    /** {@inheritdoc} */
    public function read($session_id)
    {
        return $this->memcached->get($this->getMemcachedKey($session_id)) ?: '';
    }

    /** {@inheritdoc} */
    public function write($session_id, $session_data)
    {
        return $this->memcached->set($this->getMemcachedKey($session_id), $session_data, time() + $this->gcMaxLifeTime);
    }

    public function sessionLock(string $key): bool
    {
        $timeout = $this->maxLockTime * 1000000;
        $expireTimeout = $timeout / 500000;
        $lockSessionName = $this->getSessionLockName($key);
        $isSet = false;
        while (!$isSet && $timeout >= 0) {
            $isSet = $this->memcached->add($lockSessionName, uniqid(), time() + $expireTimeout);
            if ($isSet) {
                return true;
            }
            $usleepVal = rand(100, 300);
            usleep($usleepVal);
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
        return 'lock_'.session_id().'_'.$key;
    }

    public function getMemcached(): \Memcached
    {
        return $this->memcached;
    }

    private function getMemcachedKey(string $session_id): string
    {
        return $this->prefix.':'.$session_id;
    }
}
