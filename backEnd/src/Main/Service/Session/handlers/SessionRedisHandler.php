<?php

namespace Main\Service\Session\handlers;

class SessionRedisHandler implements \SessionHandlerInterface
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
        return $this->redis->get($this->getRedisKey($session_id));
    }

    public function write($session_id, $session_data)
    {
        $this->redis->set($this->getRedisKey($session_id), $session_data, $this->gcMaxLifeTime);
    }

    private function getRedisKey($session_id)
    {
        return $this->prefix.':'.$session_id;
    }
}
