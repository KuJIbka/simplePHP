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

    public function saveWithTags($key, $value, array $tags, $expire = 0)
    {
        $data = [
            'v' => $value,
            't' => [],
        ];
        foreach ($tags as $tag) {
            $data['t'][] = [ $this->getTagKey($tag) => $_SERVER['REQUEST_TIME'] ];
        }
        return $this->getCacheDriver()->save($key, $data, $expire);
    }

    public function setTagsTimestamp(array $tags)
    {
        $tagsTime = $_SERVER['REQUEST_TIME'];
        foreach ($tags as $tag) {
            if ($tag !== null) {
                $this->getCacheDriver()->save($this->getTagKey($tag), $tagsTime, 0);
            }
        }
    }

    public function fetchTags($key, array $tags, $notFoundFunc, $expire = 0)
    {
        $cachedValue = null;
        $founded = false;
        $result = $this->getCacheDriver()->fetch($key);
        if ($result) {
            $cachedValue = $result['v'];
            $cachedTags = $result['t'];
            $founded = !$this->checkTagsIsExpired($cachedTags);
        }
        if ($founded) {
            $result = $cachedValue;
        } else {
            $this->lock($key);
            $result = call_user_func($notFoundFunc);
            $this->saveWithTags($key, $result, $tags, $expire);
            $this->unlock($key);
        }
        return $result;
    }

    public function checkTagsIsExpired(array $tags): bool
    {
        $currentTagsTimes = $this->getCacheDriver()->fetchMultiple(array_keys($tags));
        foreach ($tags as $tagName => $tagTime) {
            $fromCache = isset($currentTagsTimes[$tagName]) ? $currentTagsTimes[$tagName] : null;
            if (!$fromCache || $fromCache !== $tagTime) {
                return true;
            }
        }
        return false;
    }

    public function lock($key)
    {
        $lockedKey = $this->getLockKey($key);
        $timeout = Config::get()->getParam('cache_lock_timeout');
        $result = false;
        while ($timeout > 0) {
            if ($this->getCacheDriver()->contains($lockedKey)) {
                $usleepVal = rand(100, 300);
                usleep($usleepVal);
                $timeout -= $usleepVal;
                continue;
            }
            $result = $this->getCacheDriver()->save($lockedKey, 1, Config::get()->getParam('cache_lock_expire'));
        }
        if (!$result) {
            throw new \Exception("Cache can't' lock key for timeout");
        }
    }

    public function unlock($key)
    {
        return $this->getCacheDriver()->delete($this->getLockKey($key));
    }

    public function getTagKey($key)
    {
        return 'tag_'.$key;
    }

    public function getLockKey($key)
    {
        return 'lock_'.$key;
    }
}
