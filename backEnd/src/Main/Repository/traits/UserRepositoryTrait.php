<?php

namespace Main\Repository\traits;

use Main\Repository\UserRepository;

trait UserRepositoryTrait
{
    /** @var UserRepository */
    protected $userRepository;

    public function setUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
}
