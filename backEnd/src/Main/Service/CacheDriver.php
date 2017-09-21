<?php

namespace Main\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\RedisCache;
use Main\Utils\AbstractSingleton;

/**
 * @method static CacheDriver get()
 */
class CacheDriver extends AbstractSingleton
{
    const DRIVER_ARRAY = 'array';
    const DRIVER_REDIS = 'redis';

    protected static $inst;
    protected $cacheDriver;

    protected function init()
    {
        parent::init();
        switch (Config::get()->getParam('cache_driver')) {
            case self::DRIVER_REDIS:
                $this->cacheDriver = new RedisCache();
                $redis = new \Redis();
                $redis->connect(
                    Config::get()->getParam('redis_host'),
                    Config::get()->getParam('redis_port'),
                    Config::get()->getParam('redis_timeout'),
                    null,
                    Config::get()->getParam('redis_retryInterval')
                );
                $redisDriver = new RedisCache();
                $redisDriver->setRedis($redis);
                $this->cacheDriver = $redisDriver;
                break;

            default:
                $this->cacheDriver = new ArrayCache();

        }
    }

    /**
     * @return CacheProvider
     */
    public function getCacheDriver()
    {
        return $this->cacheDriver;
    }
}
