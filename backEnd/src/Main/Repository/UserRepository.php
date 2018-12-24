<?php

namespace Main\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\TransactionRequiredException;
use Main\Exception\UserNotFound;
use Main\Entity\User;
use Main\Filter\UserFilter;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User findOneBy(array $criteria, array $orderBy = null)
 */
class UserRepository extends BaseRepository
{
    /**
     * @param int $id
     * @return User|null
     * @throws UserNotFound
     */
    public function getById(int $id): ?User
    {
        $user = $this->find($id);
        if (!$user) {
            throw new UserNotFound();
        }
        return $user;
    }

    public function findByLogin($login): ?User
    {
        return $this->findOneBy([
            User::P_LOGIN => $login,
        ]);
    }

    /**
     * @param UserFilter $filter
     * @param int|null $start
     * @param int|null $count
     * @return User[]
     *
     * @throws TransactionRequiredException
     */
    public function findByFilter(UserFilter $filter, int $limit = 1, int $offset = null)
    {
        if ($filter->isEmpty()) {
            return [];
        }
        $qb = $this->getQBByFilter($filter);
        $limit && $qb->setMaxResults($limit);
        $offset && $qb->setFirstResult($offset);
        $q = $qb->getQuery();
        if ($filter->isForUpdate()) {
            $q->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }
        return $q->getResult();
    }

    protected function getQBByFilter(UserFilter $filter): QueryBuilder
    {
        $alias = 'u';
        $prefix = $alias.'.';
        $qb = $this->createQueryBuilder($alias);
        if (!$filter->isEmpty()) {
            $andX = $qb->expr()->andX();
            if (!is_null($filter->getIds())) {
                $andX->add($qb->expr()->in($prefix.User::P_ID, $filter->getIds()));
            }
            if ($andX->count() > 0) {
                $qb->andWhere($andX);
            }
        }
        return $qb;
    }
}
