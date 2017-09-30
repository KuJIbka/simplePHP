<?php
namespace Main\Controller;

use Main\Exception\BaseException;
use Main\Service\DB;
use Main\Service\SessionManager;

class MainController extends BaseController
{
    public function index()
    {
        DB::get()->getEm()->beginTransaction();
        try {
            return $this->render("main.html.twig");
        } catch (BaseException $e) {
            return $e->getMessage();
        }
    }

    public function in()
    {
        $user = SessionManager::get()->getLoggedUser();
        return $this->render("in.html.twig", [ 'user' => $user ]);
    }
}
