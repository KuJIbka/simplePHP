<?php

namespace Main\Repository;

use Doctrine\ORM\EntityRepository;
use Main\Entity\Role;

/**
 * @method Role|null find($id, $lockMode = null, $lockVersion = null)
 * @method Role[] findAll()
 * @method Role[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Role findOneBy(array $criteria, array $orderBy = null)
 */
class RoleRepository extends EntityRepository
{
    const CN_ID = 'id';
    const CN_NAME = 'name';

    /**
     * @return Role[]
     */
    public function getRolesWithPermissions(): array
    {
        $alias = 'r';
        $qb = $this->createQueryBuilder($alias);
        $q = $qb->leftJoin('r.permissions', 'p')->addSelect('p')->getQuery();
        $roles = $q->getResult();
        return $roles;
    }

    public function getByName(string $roleName): ?Role
    {
        return $this->findOneBy([self::CN_NAME => $roleName]);
    }
}
