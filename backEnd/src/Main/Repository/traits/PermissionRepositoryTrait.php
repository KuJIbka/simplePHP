<?php

namespace Main\Repository\traits;

use Main\Repository\PermissionRepository;

trait PermissionRepositoryTrait
{
    /** @var PermissionRepository */
    protected $permissionRepository;

    public function setPermissionRepository(PermissionRepository $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }
}
