<?php

namespace Main\Repository;

use Doctrine\ORM\EntityRepository;
use Main\Exception\UserNotFound;
use Main\Entity\User;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User findOneBy(array $criteria, array $orderBy = null)
 */
class UserRepository extends EntityRepository
{
    const CN_NAME = 'name';
    const CN_LOGIN = 'login';
    const CN_PASSWORD = 'password';
    const CN_BALANCE = 'balance';

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
            self::CN_LOGIN => $login,
        ]);
    }
}
