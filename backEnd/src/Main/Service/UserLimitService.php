<?php

namespace Main\Service;

use Main\Entity\UserLimit;
use Main\Utils\AbstractSingleton;

/**
 * @method static UserLimitService get()
 */
class UserLimitService extends AbstractSingleton
{
    static protected $inst;

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
    }
}
