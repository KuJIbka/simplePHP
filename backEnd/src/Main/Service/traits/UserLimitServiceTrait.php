<?php

namespace Main\Service\traits;

use Main\Service\UserLimitService;

trait UserLimitServiceTrait
{
    /** @var UserLimitService */
    protected $userLimitService;

    public function setUserLimitService(UserLimitService $userLimitService)
    {
        $this->userLimitService = $userLimitService;
    }
}
