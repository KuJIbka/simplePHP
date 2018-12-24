<?php

namespace Main\Service;

use Main\Entity\User;
use Main\Repository\RoleRepository;

/**
 * @method static PermissionService get()
 */
class PermissionService
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER_SIMPLE = 'ROLE_USER_SIMPLE';
    const ROLE_USER_GUEST = 'ROLE_USER_GUEST';

    const ACTION_ADMIN_LOGIN = 'ACTION_ADMIN_LOGIN';

    const ACTION_MAIN_IS_AUTHENTICATED_FULLY = 'ACTION_MAIN_IS_AUTHENTICATED_FULLY';
    const ACTION_MAIN_CAN_LOGIN = 'ACTION_CAN_LOGIN';

    protected $loadedUsersPermissions;

    /** @var CacheDriver */
    protected $cacheDriver;
    /** @var RoleRepository */
    protected $roleRepository;

    public function __construct(
        CacheDriver $cacheDriver,
        RoleRepository $roleRepository
    ) {
        $this->cacheDriver = $cacheDriver;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param User $user
     * @param string $permissionName
     * @return bool
     * @throws \Exception
     */
    public function isGranted(User $user, string $permissionName): bool
    {
        return isset($this->getPermissionForUser($user)[$permissionName]);
    }

    /**
     * @param User $user
     * @return array
     * @throws \Exception
     */
    public function getPermissionForUser(User $user): array
    {
        if (!isset($this->loadedUsersPermissions[$user->getId()])) {
            $this->loadedUsersPermissions[$user->getId()] = $this->buildUserPermissions($user);
        }
        return $this->loadedUsersPermissions[$user->getId()];
    }

    /**
     * @param User $user
     * @return array
     * @throws \Exception
     */
    private function buildUserPermissions(User $user): array
    {
        $tree = $permissionTree = $this->getPermissionTree();
        $permissions = [];
        foreach ($user->getRoles() as $role) {
            $permissions = array_merge($permissions, $tree[$role->getName()]);
        }
        return $permissions;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getPermissionTree(): array
    {
        $tagsForPermTree = [ CacheDriver::TAG_PERMISSIONS, CacheDriver::TAG_ROLES ];
        $cacheDriver = $this->cacheDriver;
        return $cacheDriver->fetchTaggedOrUpdate(
            CacheDriver::KEY_PERMISSION_TREE,
            $tagsForPermTree,
            function () use ($tagsForPermTree, $cacheDriver) {
                $tree = [];
                $roles = $this->roleRepository->getRolesWithPermissions();

                foreach ($roles as $role) {
                    $tree[$role->getName()][$role->getName()] = true;
                    foreach ($role->getPermissions() as $permission) {
                        $tree[$role->getName()][$permission->getName()] = true;
                    }
                }
                return $tree;
            }
        );
    }

    public function clearPermissionCache()
    {
        $this->cacheDriver->getCacheDriver()->deleteMultiple([CacheDriver::TAG_ROLES, CacheDriver::TAG_PERMISSIONS]);
    }
}
