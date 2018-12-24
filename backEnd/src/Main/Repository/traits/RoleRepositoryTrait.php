<?php

namespace Main\Repository\traits;

use Main\Repository\RoleRepository;

trait RoleRepositoryTrait
{
    /** @var RoleRepository */
    protected $roleRepository;

    public function setRoleRepository(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
}
