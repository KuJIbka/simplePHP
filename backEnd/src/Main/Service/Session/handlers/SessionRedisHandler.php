<?php

namespace Main\Service\Session\handlers;

use Main\Exception\CommonFatalError;

class SessionRedisHandler extends SessionHandlerAbstract
{
    /** @var \Redis */
    protected $redis;
    protected $prefix = '';
    protected $gcMaxLifeTime = 0;
    protected $maxLockTime = 0;

    public function __construct(
        \Redis $redis,
        int $gcMaxLifeTime = 1440,
        int $maxLockTime = 15,
        $prefix = 'PHPSESSID'
    ) {
        $this->redis = $redis;
        $this->prefix = $prefix;
        $this->gcMaxLifeTime = $gcMaxLifeTime;
        $this->maxLockTime = $maxLockTime;
    }

    public function sessionLock(string $key): bool
    {
        $timeout = $this->maxLockTime * 1000000;
        $expireTimeout = $timeout / 500000;
        $lockSessionName = $this->getSessionLockName($key);
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
            usleep($usleepVal * 1000);
            $timeout -= $usleepVal;
        }
        if (!$isSet && $timeout < 0) {
            throw new CommonFatalError();
        }
        return false;
    }

    public function sessionUnlock(string $lockName)
    {
        $this->redis->delete($this->getSessionLockName($lockName));
    }

    public function getSessionLockName(String $key): string
    {
        return 'lock_'.$key;
    }

    private function getRedisKey(string $session_id): string
    {
        return $this->prefix.':'.$session_id;
    }

    public function getRedis(): \Redis
    {
        return $this->redis;
    }

    public function __destruct()
    {
        $this->close();
    }

    protected function doRead(string $session_id): string
    {
        return $this->redis->get($this->getRedisKey($session_id)) ?: '';
    }

    protected function doWrite(string $session_id, string $session_data): bool
    {
        return $this->redis->set($this->getRedisKey($session_id), $session_data, $this->gcMaxLifeTime);
    }

    protected function doDestroy(string $session_id): bool
    {
        $this->redis->del($this->getRedisKey($session_id));
        return true;
    }

    protected function doClose(): bool
    {
        return true;
    }

    /** {@inheritdoc} */
    public function updateTimestamp($key, $val)
    {
        $this->redis->expire($this->getRedisKey($key), $this->gcMaxLifeTime);
    }

    public function gc($maxlifetime)
    {
        return true;
    }
}
