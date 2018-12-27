<?php

namespace Main\Controller;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use Main\Exception\BaseException;
use Main\Form\Data\LoginFormData;
use Main\Exception\WrongData;
use Main\Factory\ResponseFactory;
use Main\Repository\traits\UserLimitRepositoryTrait;
use Main\Repository\traits\UserRepositoryTrait;
use Main\Service\PermissionService;
use Main\Service\Session\SessionManager;
use Main\Service\traits\EntityManagerTrait;
use Main\Service\traits\PermissionServiceTrait;
use Main\Service\traits\RouterTrait;
use Main\Service\traits\UserLimitServiceTrait;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    use UserLimitServiceTrait,
        UserLimitRepositoryTrait,
        PermissionServiceTrait,
        EntityManagerTrait,
        UserRepositoryTrait,
        RouterTrait;

    /**
     * @return Response
     * @throws OptimisticLockException
     * @throws \Exception
     */
    public function login()
    {
        $responseData = $this->responseFactory->getDefaultResponseData(
            ResponseFactory::RESP_TYPE_ERROR,
            'L_ERROR_BAD_COMBINATION_OF_ACCOUNT_DATA'
        );
        $resp = $this->responseFactory->getJsonResponse($responseData);
        try {
            if (!$this->permissionService->isGranted(
                $this->sessionManager->getLoggedUser(),
                PermissionService::ACTION_MAIN_CAN_LOGIN
            )) {
                throw new WrongData();
            }
            $data = new LoginFormData($_POST);
            $data->isValidWithThrowException();
            $login = $data->getLogin();
            $password = $data->getPassword();

            $this->entityManager->beginTransaction();
            $user = $this->userRepository->findByLogin($login);
            if ($user) {
                $userLimit = $this->userLimitRepository->find($user->getId(), LockMode::PESSIMISTIC_WRITE);
                if ($this->userLimitService->checkLoginCount($userLimit)) {
                    if (!password_verify($password, $user->getPassword())) {
                        $this->userLimitService->changeLoginCount($userLimit, 1);
                        $this->entityManager->persist($userLimit);
                    } else {
                        $this->userLimitService->clearLoginCount($userLimit);
                        $this->entityManager->persist($userLimit);
                        $this->sessionManager->open();
                        $this->sessionManager->regenerateId(true);
                        $this->sessionManager->setLoggedUser($user);
                        $this->sessionManager->close();
                        $responseData = $this->responseFactory->getDefaultResponseData(
                            ResponseFactory::RESP_TYPE_SUCCESS,
                            '',
                            '/in',
                            [ 'userData' => $user ]
                        );
                        $resp = $this->responseFactory->getJsonResponse($responseData);
                        if ($this->appRequest->getLocale() !== $user->getLang()) {
                            $user->setLang($this->appRequest->getLocale());
                            $this->entityManager->persist($user);
                        }
                    }
                    $this->entityManager->flush();
                    $this->entityManager->commit();
                }
            }
        } catch (BaseException $e) {
            $this->entityManager->getConnection()->isTransactionActive() && $this->entityManager->rollback();
            $resp = $this->responseFactory->exceptionToResponse($e);
        }
        return $resp;
    }

    /**
     * @return Response
     * @throws WrongData
     */
    public function logout()
    {
        if ($this->sessionManager->isLogged()) {
            $csrfToken = $this->sessionManager->getParam(SessionManager::KEY_CSRF_TOKEN);
            $this->sessionManager->open();
            $this->sessionManager->regenerateId(true);
            $this->sessionManager->destroySession();
            $this->sessionManager->close();

            $this->sessionManager->open();
            $this->sessionManager->setParam(SessionManager::KEY_CSRF_TOKEN, $csrfToken);
            $this->sessionManager->close();
            return $this->responseFactory->getJsonResponse($this->responseFactory->getDefaultResponseData(
                ResponseFactory::RESP_TYPE_SUCCESS,
                '',
                '/'
            ));
        } else {
            throw new WrongData();
        }
    }

    public function getUserSettings()
    {
        return $this->responseFactory->getCommonSuccessResponse(
            [ 'userData' =>  $this->sessionManager->getLoggedUser() ],
            ''
        );
    }
}
