<?php

namespace Main\Service\Session\handlers;

use Main\Exception\CommonFatalError;

class SessionRedisHandler implements MainSessionHandlerInterface
{
    /** @var \Redis */
    protected $redis;
    protected $prefix = '';
    protected $gcMaxLifeTime = 0;

    public function __construct(\Redis $redis, int $gcMaxLifeTime = 1440, $prefix = 'PHPSESSID')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->gcMaxLifeTime = $gcMaxLifeTime;
    }

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        $this->redis->del($this->getRedisKey($session_id));
        return true;
    }

    public function gc($maxlifetime)
    {
        return true;
    }

    public function open($save_path, $name)
    {
        return true;
    }

    public function read($session_id)
    {
        return $this->redis->get($this->getRedisKey($session_id)) ?: '';
    }

    public function write($session_id, $session_data)
    {
        $this->redis->set($this->getRedisKey($session_id), $session_data, $this->gcMaxLifeTime);
    }

    private function getRedisKey($session_id)
    {
        return $this->prefix.':'.$session_id;
    }

    public function sessionLock(string $lockName): bool
    {
        $timeout = 15000000;
        $expireTimeout = $timeout / 500000;
        $lockSessionName = $this->getSessionLockKeyName($lockName);
        $isSet = false;
        while (!$isSet && $timeout >= 0) {
            $isSet = $this->redis->set(
                $lockSessionName,
                uniqid(),
                [ 'NX', 'EX' => $expireTimeout ]
            );
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

    public function sessionUnlock(string $lockName)
    {
        $this->redis->delete($this->getSessionLockKeyName($lockName));
    }

    public function getSessionLockKeyName(String $key): string
    {
        return 'lock_'.session_id().'_'.$key;
    }
}
