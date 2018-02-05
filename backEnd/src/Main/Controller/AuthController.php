<?php

namespace Main\Controller;

use Doctrine\DBAL\LockMode;
use Main\Exception\BaseException;
use Main\Form\Data\LoginFormData;
use Main\Exception\WrongData;
use Main\Factory\ResponseFactory;
use Main\Service\DB;
use Main\Service\PermissionService;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Main\Service\UserLimitService;
use Main\Struct\DefaultResponseData;
use Sabre\HTTP\Response;

class AuthController extends BaseController
{
    /**
     * @return Response
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function login()
    {
        $sessionManager = SessionManager::get();
        $responseData = new DefaultResponseData(
            ResponseFactory::RESP_TYPE_ERROR,
            'L_ERROR_BAD_COMBINATION_OF_ACCOUNT_DATA'
        );
        $resp = ResponseFactory::getJsonResponse($responseData);
        try {
            if (!PermissionService::get()->isGranted(
                $sessionManager->getLoggedUser(),
                PermissionService::ACTION_MAIN_CAN_LOGIN
            )) {
                throw new WrongData();
            }
            $data = new LoginFormData($_POST, true);
            $data->isValidWithThrowException();
            $login = $data->getLogin();
            $password = $data->getPassword();
            $userRepository = DB::get()->getUserRepository();
            $userLimitRepository = DB::get()->getUserLimitRepository();
            $userLimitService = UserLimitService::get();

            DB::get()->getEm()->beginTransaction();
            $user = $userRepository->findByLogin($login);
            if ($user) {
                $userLimit = $userLimitRepository->find($user->getId(), LockMode::PESSIMISTIC_WRITE);
                if ($userLimitService->checkLoginCount($userLimit)) {
                    if (!password_verify($password, $user->getPassword())) {
                        $userLimitService->changeLoginCount($userLimit, 1);
                        DB::get()->getEm()->persist($userLimit);
                    } else {
                        $userLimitService->clearLoginCount($userLimit);
                        DB::get()->getEm()->persist($userLimit);
                        $sessionManager->open();
                        $sessionManager->regenerateId(true);
                        $sessionManager->setLoggedUser($user);
                        $sessionManager->close();
                        $responseData = new DefaultResponseData(
                            ResponseFactory::RESP_TYPE_SUCCESS,
                            '',
                            '/in',
                            [ 'userData' => $user ]
                        );
                        $resp = ResponseFactory::getJsonResponse($responseData);
                        if (Router::get()->getRequestLocale() !== $user->getLang()) {
                            $user->setLang(Router::get()->getRequestLocale());
                            DB::get()->getEm()->persist($user);
                        }
                    }
                    DB::get()->getEm()->flush();
                    DB::get()->getEm()->commit();
                }
            }
        } catch (BaseException $e) {
            DB::get()->getEm()->getConnection()->isTransactionActive() && DB::get()->getEm()->rollback();
            $resp = ResponseFactory::exceptionToResponse($e);
        }
        return $resp;
    }

    /**
     * @return Response
     * @throws WrongData
     */
    public function logout()
    {
        $sessionManager = SessionManager::get();
        if ($sessionManager->isLogged()) {
            $sessionManager->open();
            $sessionManager->regenerateId(true);
            $sessionManager->destroySession();
            $sessionManager->close();
            return ResponseFactory::getJsonResponse(new DefaultResponseData(
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
        return ResponseFactory::getCommonSuccessResponse(
            [ 'userData' => SessionManager::get()->isLogged() ? SessionManager::get()->getLoggedUser() : null ],
            ''
        );
    }
}
