<?php

namespace Main\Service\traits;

use Main\Service\AppEventDispatcher;

trait AppEventDispatcherTrait
{
    /** @var AppEventDispatcher */
    protected $appEventDispatcher;

    public function setAppEventDispatcher(AppEventDispatcher $appEventDispatcher)
    {
        $this->appEventDispatcher = $appEventDispatcher;
    }
}
