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
        $this->cacheDriver->setNamespace(Config::get()->getParam('cache_namespace'));
    }

    /**
     * @return CacheProvider
     */
    public function getCacheDriver()
    {
        return $this->cacheDriver;
    }

    public function saveWithTags($key, $value, array $tags, $expire = 0): bool
    {
        $data = [
            'v' => $value,
            't' => [],
        ];
        foreach ($tags as $tag) {
            $data['t'][$this->getTagKey($tag)] = $_SERVER['REQUEST_TIME'];
        }
        return $this->getCacheDriver()->save($key, $data, $expire);
    }

    public function setTagsTimestamp(array $tags)
    {
        foreach ($tags as $tag) {
            $this->getCacheDriver()->save($this->getTagKey($tag), $_SERVER['REQUEST_TIME'], 0);
        }
    }

    public function fetchTaggedOrUpdate($key, array $tags, $notFoundFunc, $expire = 0): mixed
    {
        $result = $this->fetchTagged($key);
        if ($result === false) {
            if (is_callable($notFoundFunc)) {
                $this->lock($key);
                $result = call_user_func($notFoundFunc);
                $this->saveWithTags($key, $result, $tags, $expire);
                $this->unlock($key);
            }
        }
        return $result;
    }

    public function fetchTagged($key): mixed
    {
        $result = $this->getCacheDriver()->fetch($key);
        if ($result !== false) {
            $cachedValue = $result['v'];
            $cachedTags = $result['t'];
            if (!$this->checkTagsIsExpired($cachedTags)) {
                $result = $cachedValue;
            } else {
                $result = false;
            }
        }
        return $result;
    }

    public function checkTagsIsExpired(array $tags): bool
    {
        $currentTagsTimes = $this->getCacheDriver()->fetchMultiple(array_keys($tags));
        foreach ($tags as $tagName => $tagTime) {
            $fromCache = isset($currentTagsTimes[$tagName]) ? $currentTagsTimes[$tagName] : null;
            if (is_null($fromCache) || $fromCache > $tagTime) {
                return true;
            }
        }
        return false;
    }

    public function lock($key)
    {
        $lockedKey = $this->getLockKey($key);
        $timeout = Config::get()->getParam('cache_lock_try_timeout');
        $result = false;
        while ($timeout > 0) {
            if ($this->getCacheDriver()->contains($lockedKey)) {
                $usleepVal = rand(100, 300);
                usleep($usleepVal);
                $timeout -= $usleepVal;
                continue;
            }
            $result = $this->getCacheDriver()->save($lockedKey, 1, Config::get()->getParam('cache_lock_expire'));
            break;
        }
        if (!$result) {
            throw new \Exception("Cache can't' lock key for timeout");
        }
    }

    public function unlock($key): bool
    {
        return $this->getCacheDriver()->delete($this->getLockKey($key));
    }

    public function getTagKey($key): string
    {
        return 'tag_'.$key;
    }

    public function getLockKey($key): string
    {
        return 'lock_'.$key;
    }
}
