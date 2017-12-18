<?php
namespace Main\Controller;

use Main\Factory\ResponseFactory;
use Main\Service\Config;
use Main\Service\Router;
use Main\Service\Session\SessionManager;

class MainController extends BaseController
{
    public function index()
    {
        return $this->render("react.html.twig");
    }

    public function setDefaultLang()
    {
        $resultLang = Config::get()->getParam('language_default_lang');
        $availableLangs = Config::get()->getParam('language_available_langs');
        if (SessionManager::get()->isLogged()) {
            $user = SessionManager::get()->getLoggedUser();
            $resultLang = $user->getLang();
        } elseif (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], $availableLangs)) {
            $resultLang = $_COOKIE['lang'];
        }
        return ResponseFactory::getSimpleResponse(
            null,
            302,
            [
                'Location' => Router::get()->getUrlGenerator()->generate(
                    'main_page',
                    [ '_locale' => $resultLang ]
                )
            ]
        );
    }

    public function in()
    {
        $user = SessionManager::get()->getLoggedUser();
        return $this->render("in.html.twig", [ 'user' => $user ]);
    }
}
