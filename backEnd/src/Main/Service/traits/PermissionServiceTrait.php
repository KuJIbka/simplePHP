<?php

namespace Main\Service\traits;

use Main\Service\PermissionService;

trait PermissionServiceTrait
{
    /** @var PermissionService */
    protected $permissionService;

    public function setPermissionService(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }
}
