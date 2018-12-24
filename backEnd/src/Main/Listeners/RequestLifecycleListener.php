<?php

namespace Main\Listeners;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Main\Events\RequestLifecycleBeforeMethodCall;
use Main\Service\traits\EntityManagerTrait;
use Main\Service\traits\SessionManagerTrait;

class RequestLifecycleListener
{
    use SessionManagerTrait,
        EntityManagerTrait;

    /**
     * @param RequestLifecycleBeforeMethodCall $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function onBeforeMethodCall(RequestLifecycleBeforeMethodCall $event)
    {
        if ($this->sessionManager->isLogged()) {
            $appRequest = $event->getAppRequest();
            $user = $this->sessionManager->getLoggedUser();
            $locale = $appRequest->getLocale();
            if ($locale && $locale !== $user->getLang()) {
                $user->setLang($locale);
                $this->entityManager->flush();
            }
        }
    }
}
