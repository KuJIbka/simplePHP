<?php

namespace Main\Service;

use Main\Entity\User;
use Main\Entity\UserLimit;
use Main\Utils\AbstractSingleton;

/**
 * @method static UserService get()
 */
class UserService extends AbstractSingleton
{
    protected static $inst;

    public function encryptPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, [ 'cost' => 10 ]);
    }

    /**
     * @param string $login
     * @param string $password
     * @return User
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addNewUser(string $login, string $password): User
    {
        $user = (new User())->setLogin($login)->setPassword($password);
        DB::get()->getEm()->persist($user);
        DB::get()->getEm()->flush();
        $userLimit = (new UserLimit())->setUser($user);
        DB::get()->getEm()->persist($userLimit);
        DB::get()->getEm()->flush();
        return $user;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeUser(User $user)
    {
        $userLimit = $user->getUserLimit();
        DB::get()->getEm()->remove($userLimit);
        DB::get()->getEm()->remove($user);
        DB::get()->getEm()->flush();
    }
}
