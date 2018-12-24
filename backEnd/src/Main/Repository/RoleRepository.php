<?php

namespace Main\Repository;

use Main\Entity\Role;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role[] findAll()
 * @method Role[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Role findOneBy(array $criteria, array $orderBy = null)
 */
class RoleRepository extends BaseRepository
{
    /**
     * @return Role[]
     */
    public function getRolesWithPermissions(): array
    {
        $alias = 'r';
        $prefix = $alias.'.';
        $qb = $this->createQueryBuilder($alias);
        $q = $qb->leftJoin($prefix.Role::P_PERMISSIONS, 'p')->addSelect('p')->getQuery();
        $roles = $q->getResult();
        return $roles;
    }

    public function getByName(string $roleName): ?Role
    {
        return $this->findOneBy([Role::P_NAME => $roleName]);
    }
}
