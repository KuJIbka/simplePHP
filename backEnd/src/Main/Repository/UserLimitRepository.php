<?php

namespace Main\Repository;

use Doctrine\ORM\EntityRepository;
use Main\Entity\UserLimit;
use Main\Service\Config;

/**
 * @method UserLimit|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserLimit[] findAll()
 * @method UserLimit[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method UserLimit findOneBy(array $criteria, array $orderBy = null)
 */
class UserLimitRepository extends EntityRepository
{
    const CN_USER_ID = 'user_id';
    const CN_LOGIN_TRY_COUNT = 'login_try_count';
    const CN_LOGIN_TRY_COUNT_TIME = 'login_try_count_time';

    public function checkLoginCount(UserLimit $userLimit): bool
    {
        $maxCount = Config::get()->getParam('loginCountMax');
        $maxCountTime = Config::get()->getParam('loginCountMaxTime');
        if ($_SERVER['REQUEST_TIME'] - $userLimit->getLoginTryCountTime() < $maxCountTime
            && $userLimit->getLoginTryCount() >= $maxCount
        ) {
            return false;
        }
        return true;
    }

    public function clearLoginCount(UserLimit $userLimit)
    {
        $userLimit->setLoginTryCount(0)->setLoginTryCount(0);
    }

    public function changeLoginCount(UserLimit $userLimit, int $val)
    {
        $prevValue = $userLimit->getLoginTryCount();
        $userLimit->setLoginTryCount($prevValue + $val)->setLoginTryCountTime($_SERVER['REQUEST_TIME']);
        $this->getEntityManager()->persist($userLimit);
    }
}
