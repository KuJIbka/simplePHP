<?php

namespace Main\Service\traits;

use Main\Service\CacheDriver;

trait CacheDriverTrait
{
    /** @var CacheDriver */
    protected $cacheDriver;

    public function setCacheDriverService(CacheDriver $cacheDriver)
    {
        $this->cacheDriver = $cacheDriver;
    }
}
