<?php

namespace Main\Repository;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Andx;
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
    public function findByFilter(UserFilter $filter, $start, $count)
    {
        if ($filter->isEmpty()) {
            return [];
        }
        $alias = 'u';
        $qb = $this->createQueryBuilder($alias);
        $q = $qb->select()->where($this->getExprByFilter($filter, $alias))
            ->setFirstResult($start)
            ->setMaxResults($count)
            ->getQuery();
        if ($filter->isForUpdate()) {
            $q->setLockMode(LockMode::PESSIMISTIC_WRITE);
        }
        return $q->getResult();
    }

    public function getExprByFilter(UserFilter $filter, string $alias): AndX
    {
        $qb = $this->createQueryBuilder($alias);
        $andX = $qb->expr()->andX();
        if (!is_null($filter->getIds())) {
            $andX->add($qb->expr()->in($alias.'.id', $filter->getIds()));
        }
        return $andX;
    }
}
