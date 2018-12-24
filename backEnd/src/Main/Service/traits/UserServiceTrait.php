<?php

namespace Main\Service\traits;

use Main\Service\UserService;

class UserServiceTrait
{
    /** @var UserService */
    protected $userService;

    public function setUserService(UserService $userService)
    {
        $this->userService = $userService;
    }
}
