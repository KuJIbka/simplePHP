<?php

namespace Main\Repository;

use Doctrine\ORM\EntityRepository;
use Main\Entity\Permission;

/**
 * @method Permission|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permission[] findAll()
 * @method Permission[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Permission findOneBy(array $criteria, array $orderBy = null)
 */
class PermissionRepository extends EntityRepository
{
}
