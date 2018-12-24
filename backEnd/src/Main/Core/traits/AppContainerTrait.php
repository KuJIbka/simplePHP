<?php

namespace Main\Core\traits;

use Main\Core\AppContainer;

trait AppContainerTrait
{
    /** @var AppContainer */
    protected $appContainer;

    public function setAppContainer(AppContainer $appContainer)
    {
        $this->appContainer = $appContainer;
    }
}
