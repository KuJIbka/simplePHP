<?php

namespace Main\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
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
class UserRepository extends EntityRepository
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
    public function findByFilter(UserFilter $filter, int $start = null, int $count = null)
    {
        if ($filter->isEmpty()) {
            return [];
        }
        $uAlias = 'u';
        $qb = $this->createQueryBuilder($uAlias);
        $qb = $this->getQBByFilter($qb, $filter, $uAlias);
        $start && $qb->setFirstResult($start);
        $count && $qb->setMaxResults($count);
        $q = $qb->getQuery();
        if ($filter->isForUpdate()) {
            $q->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }
        return $q->getResult();
    }

    public function getQBByFilter(QueryBuilder $qb, UserFilter $filter, string $alias): QueryBuilder
    {
        $andX = $qb->expr()->andX();
        if (!is_null($filter->getIds())) {
            $andX->add($qb->expr()->in($alias.'.id', $filter->getIds()));
        }
        $qb->andWhere($andX);
        return $qb;
    }
}
