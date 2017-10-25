<?php

namespace Main\Service\Session\handlers;

use Main\Exception\CommonFatalError;

class SessionRedisHandler implements MainSessionHandlerInterface
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

    /** {@inheritdoc} */
    public function close()
    {
        return true;
    }

    /** {@inheritdoc} */
    public function destroy($session_id)
    {
        $this->redis->del($this->getRedisKey($session_id));
        return true;
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
        return $this->redis->get($this->getRedisKey($session_id)) ?: '';
    }

    /** {@inheritdoc} */
    public function write($session_id, $session_data)
    {
        return $this->redis->set($this->getRedisKey($session_id), $session_data, $this->gcMaxLifeTime);
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
        $this->redis->delete($this->getSessionLockName($lockName));
    }

    public function getSessionLockName(String $key): string
    {
        return 'lock_'.session_id().'_'.$key;
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
}
