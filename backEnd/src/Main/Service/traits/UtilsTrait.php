<?php

namespace Main\Service\traits;

use Main\Service\Utils;

trait UtilsTrait
{
    /** @var Utils */
    protected $utils;

    public function setUtils(Utils $utils)
    {
        $this->utils = $utils;
    }
}
