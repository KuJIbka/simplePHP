<?php

namespace Main\Repository\traits;

use Main\Repository\UserLimitRepository;

trait UserLimitRepositoryTrait
{
    /** @var UserLimitRepository */
    protected $userLimitRepository;

    public function setUserLimitRepository(UserLimitRepository $userLimitRepository)
    {
        $this->userLimitRepository = $userLimitRepository;
    }
}
