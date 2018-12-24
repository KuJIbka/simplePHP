<?php

namespace Main\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Main\Entity\User;
use Main\Entity\UserLimit;

class UserService
{
    /** @var EntityManager */
    protected $em;
    
    public function __construct(EntityManager $em)
    {
    }

    public function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [ 'cost' => 10 ]);
    }

    /**
     * @param string $login
     * @param string $password
     * @return User
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addNewUser(string $login, string $password): User
    {
        $user = (new User())->setLogin($login)->setPassword($password);
        $this->em->persist($user);
        $this->em->flush();
        $userLimit = (new UserLimit())->setUser($user);
        $this->em->persist($userLimit);
        $this->em->flush();
        return $user;
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function removeUser(User $user)
    {
        $userLimit = $user->getUserLimit();
        $this->em->remove($userLimit);
        $this->em->remove($user);
        $this->em->flush();
    }
}
