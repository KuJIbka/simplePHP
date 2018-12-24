<?php

namespace Main\Service\traits;

use Main\Service\Router;

trait RouterTrait
{
    /** @var Router */
    protected $router;

    public function setRouter(Router $router)
    {
        $this->router = $router;
    }
}
