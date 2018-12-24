<?php

namespace Main\Service;

use Main\Entity\UserLimit;

class UserLimitService
{
    /** @var Config */
    protected $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function checkLoginCount(UserLimit $userLimit): bool
    {
        $maxCount = $this->config->getParam('loginCountMax');
        $maxCountTime = $this->config->getParam('loginCountMaxTime');
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
