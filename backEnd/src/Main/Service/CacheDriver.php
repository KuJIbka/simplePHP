<?php

namespace Main\Service;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\RedisCache;

class CacheDriver
{
    const DRIVER_ARRAY = 'array';
    const DRIVER_REDIS = 'redis';

    const KEY_PERMISSION_TREE = 'key_permission_tree';

    const TAG_PERMISSIONS = 'tag_permissions';
    const TAG_ROLES = 'tag_roles';

    protected $cacheDriver;
    /** @var Config */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
        switch ($this->config->getParam('cache_driver')) {
            case self::DRIVER_REDIS:
                $this->cacheDriver = new RedisCache();
                $redis = new \Redis();
                $redis->connect(
                    $this->config->getParam('redis_host'),
                    $this->config->getParam('redis_port'),
                    $this->config->getParam('redis_timeout'),
                    null,
                    $this->config->getParam('redis_retryInterval')
                );
                $redisDriver = new RedisCache();
                $redisDriver->setRedis($redis);
                $this->cacheDriver = $redisDriver;
                break;

            default:
                $this->cacheDriver = new ArrayCache();
        }
        $this->cacheDriver->setNamespace($this->config->getParam('cache_namespace'));
    }

    /**
     * @return CacheProvider
     */
    public function getCacheDriver()
    {
        return $this->cacheDriver;
    }

    /**
     * @param $key
     * @param $value
     * @param array $tags
     * @param int $expire
     * @return bool
     * @throws \Exception
     */
    public function saveWithTags(string $key, $value, array $tags, int $expire = 0): bool
    {
        $data = [
            'v' => $value,
            't' => [],
        ];
        foreach ($tags as $tag) {
            if (strrpos($tag, 'tag_') !== 0) {
                throw new \Exception('Tag must start from tag_ string');
            }
            $data['t'][$tag] = $_SERVER['REQUEST_TIME_MICRO'];
        }
        return $this->getCacheDriver()->save($key, $data, $expire);
    }

    public function setTagsTimestamp(array $tags)
    {
        foreach ($tags as $tag) {
            $this->getCacheDriver()->save($tag, $_SERVER['REQUEST_TIME_MICRO'], 0);
        }
    }

    /**
     * @param $key
     * @param array $tags
     * @param $notFoundFunc
     * @param int $expire
     * @return mixed
     * @throws \Exception
     */
    public function fetchTaggedOrUpdate(string $key, array $tags, $notFoundFunc, int $expire = 0)
    {
        $result = $this->fetchTagged($key);
        if ($result === false) {
            if (is_callable($notFoundFunc)) {
                $locked = $this->lock($key);
                $result = call_user_func($notFoundFunc);
                if ($locked) {
                    $this->saveWithTags($key, $result, $tags, $expire);
                    $this->unlock($key);
                }
            }
        }
        return $result;
    }

    public function fetchTagged(string $key)
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
            if (is_null($fromCache) || $fromCache === false || $fromCache > $tagTime) {
                return true;
            }
        }
        return false;
    }

    public function lock(string $key)
    {
        $lockedKey = $this->getLockKey($key);
        $timeout = $this->config->getParam('cache_lock_try_timeout');
        $result = false;
        while ($timeout > 0) {
            if ($this->getCacheDriver()->contains($lockedKey)) {
                $usleepVal = rand(100, 300);
                usleep($usleepVal);
                $timeout -= $usleepVal;
                continue;
            }
            $result = $this->getCacheDriver()->save($lockedKey, 1, $this->config->getParam('cache_lock_expire'));
            break;
        }

        if (!$result) {
            error_log("Cache can't' lock key for timeout");
        }
        return $result;
    }

    public function unlock(string $key): bool
    {
        return $this->getCacheDriver()->delete($this->getLockKey($key));
    }

    public function getLockKey(string $key): string
    {
        return 'lock_'.$key;
    }
}
