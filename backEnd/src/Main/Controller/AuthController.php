<?php

namespace Main\Controller;

use Doctrine\DBAL\LockMode;
use Main\Form\Data\LoginFormData;
use Main\Exception\WrongData;
use Main\Factory\ResponseFactory;
use Main\Service\DB;
use Main\Service\Session\SessionManager;
use Main\Struct\DefaultResponseData;

class AuthController extends BaseController
{
    public function login()
    {
        $sessionManager = SessionManager::get();
        $responseData = new DefaultResponseData(
            ResponseFactory::RESP_TYPE_ERROR,
            'L_ERROR_BAD_COMBINATION_OF_ACCOUNT_DATA'
        );
        try {
            if ($sessionManager->isLogged()) {
                throw new WrongData();
            }
            $data = new LoginFormData($_POST);
            $data->isValidWithThrowException();
            $login = $data->getLogin();
            $password = $data->getPassword();
            $userRepository = DB::get()->getUserRepository();
            $userLimitRepository = DB::get()->getUserLimitRepository();

            DB::get()->getEm()->beginTransaction();
            $user = $userRepository->findByLogin($login);
            if ($user) {
                $userLimit = $userLimitRepository->find($user->getId(), LockMode::PESSIMISTIC_WRITE);
                if ($userLimitRepository->checkLoginCount($userLimit)) {
                    if (!password_verify($password, $user->getPassword())) {
                        $userLimitRepository->changeLoginCount($userLimit, 1);
                    } else {
                        $userLimitRepository->clearLoginCount($userLimit);
                        SessionManager::get()->open();
                        SessionManager::get()->regenerateId(true);
                        SessionManager::get()->setLoggedUser($user);
                        SessionManager::get()->close();
                        $responseData = new DefaultResponseData(
                            ResponseFactory::RESP_TYPE_SUCCESS,
                            '',
                            '/in'
                        );
                    }
                    DB::get()->getEm()->flush();
                    DB::get()->getEm()->commit();
                }
            }
        } catch (\Exception $e) {
            DB::get()->getEm()->rollback();
        }
        return ResponseFactory::getJsonResponse($responseData);
    }

    public function logout()
    {
        SessionManager::get()->open();
        SessionManager::get()->regenerateId(true);
        SessionManager::get()->close();
        return ResponseFactory::getJsonResponse(new DefaultResponseData(
            ResponseFactory::RESP_TYPE_SUCCESS,
            '',
            '/'
        ));
    }
}
