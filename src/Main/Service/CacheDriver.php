<?php

namespace Main\Service;

use Doctrine\Common\Cache\CacheProvider;
use Main\Utils\AbstractSingleton;

/**
 * @method static CacheDriver get()
 */
class CacheDriver extends AbstractSingleton
{
    protected static $inst;
    protected $cacheDriver;

    protected function init()
    {
        parent::init();
        $this->cacheDriver = new \Doctrine\Common\Cache\ArrayCache();
    }

    public function getCacheDriver(): CacheProvider
    {
        return $this->cacheDriver;
    }
}
