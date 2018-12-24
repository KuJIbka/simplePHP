<?php
namespace Main\Controller;

class MainController extends BaseRenderController
{
    public function index()
    {
        return $this->render("react.html.twig");
    }

    public function setDefaultLang()
    {
        $resultLang = $this->config->getParam('language_default_lang');
        $availableLangs = $this->config->getParam('language_available_langs');
        if ($this->sessionManager->isLogged()) {
            $user = $this->sessionManager->getLoggedUser();
            $resultLang = $user->getLang();
        } elseif (isset($_COOKIE['_locale']) && in_array($_COOKIE['_locale'], $availableLangs)) {
            $resultLang = $_COOKIE['_locale'];
        }
        return $this->responseFactory->getSimpleResponse(
            null,
            302,
            [
                'Location' => $this->router->getUrlGenerator()->generate(
                    'main_page',
                    [ '_locale' => $resultLang ]
                )
            ]
        );
    }

    public function getAppConfig()
    {
        return $this->responseFactory->getJsonResponse([
            'type' => 'success',
            'data' => $this->config->getPublicSettings(),
        ]);
    }

    public function in()
    {
        $user = $this->sessionManager->getLoggedUser();
        return $this->render("in.html.twig", [ 'user' => $user ]);
    }
}
