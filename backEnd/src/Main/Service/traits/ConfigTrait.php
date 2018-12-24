<?php

namespace Main\Service\traits;

use Main\Service\Config;

trait ConfigTrait
{
    /** @var Config */
    protected $config;

    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
