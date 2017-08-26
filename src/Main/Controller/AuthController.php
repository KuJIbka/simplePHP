<?php

namespace Main\Controller;

use Defuse\Crypto\Crypto;
use Doctrine\DBAL\LockMode;
use Main\Form\Data\LoginFormData;
use Main\Exception\BaseException;
use Main\Exception\WrongData;
use Main\Factory\ResponseFactory;
use Main\Service\Config;
use Main\Service\DB;
use Main\Service\SessionManager;
use Main\Struct\DefaultResponseData;

class AuthController extends BaseController
{
    public function login()
    {
        $sessionManager = SessionManager::get();
        if ($sessionManager->isLogged()) {
            throw new WrongData();
        }
        $data = new LoginFormData($_POST);
        if (!$data->isValid()) {
            throw new WrongData($data->getFormsErrorsData());
        }
        $login = $data->getLogin();
        $password = $data->getPassword();
        $userRepository = DB::get()->getUserRepository();
        $userLimitRepository = DB::get()->getUserLimitRepository();
        $responseData = new DefaultResponseData(
            ResponseFactory::RESP_TYPE_ERROR,
            'Пользователя с такими данными не существует'
        );
        try {
            DB::get()->getEm()->beginTransaction();
            $user = $userRepository->findByLogin($login);
            if ($user) {
                $userLimit = $userLimitRepository->find($user->getId(), LockMode::PESSIMISTIC_WRITE);
                if ($userLimitRepository->checkLoginCount($userLimit)) {
                    if (!password_verify($password, $user->getPassword())) {
                        $userLimitRepository->changeLoginCount($userLimit, 1);
                    } else {
                        $userLimitRepository->clearLoginCount($userLimit);
                        SessionManager::get()->setLoggedUser($user);
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
        SessionManager::get()->clearSession();
        return ResponseFactory::getJsonResponse(new DefaultResponseData(
            ResponseFactory::RESP_TYPE_SUCCESS,
            '',
            '/'
        ));
    }
}
