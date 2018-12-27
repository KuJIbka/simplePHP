<?php

namespace Main\Controller;

use Main\Factory\traits\ResponseFactoryTrait;
use Main\Service\traits\EntityManagerTrait;
use Main\Service\traits\PermissionServiceTrait;
use Main\Service\traits\SessionManagerTrait;
use Main\Service\traits\TranslationServiceTrait;
use Main\Struct\AppRequest;

class BaseController
{
    use ResponseFactoryTrait,
        SessionManagerTrait,
        PermissionServiceTrait,
        EntityManagerTrait,
        TranslationServiceTrait
    ;

    /** @var AppRequest */
    protected $appRequest;

    public function setAppRequest(AppRequest $appRequest): self
    {
        $this->appRequest = $appRequest;
        return $this;
    }
}
