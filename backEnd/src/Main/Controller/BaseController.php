<?php

namespace Main\Controller;

use Main\Service\Config;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Main\Service\Templater;
use Main\Service\TranslationsService;

class BaseController
{
    public function render($string, array $array = array()): string
    {
        $templates = Templater::get()->getTemplater();
        $templates->addGlobal('_locale', Router::get()->getRequestLocale());
        $csrfToken = SessionManager::get()->getParam(SessionManager::KEY_CSRF_TOKEN);
        $templates->addGlobal('csrf_token', $csrfToken);
        $templates->addGlobal('appConfig', json_encode(
            Config::get()->getPublicSettings(),
            JSON_UNESCAPED_UNICODE
        ));
        try {
            return $templates->render($string, $array);
        } catch (\Exception $e) {
            $errorText = TranslationsService::get()->getTranslator()->trans('L_COMMON_FATAL_ERROR');
            if (Config::get()->getParam('debug')) {
                $errorText = $e->getMessage();
            }
            try {
                return Templater::get()->getTemplater()->render('error.html.twig', ['errorText' => $errorText]);
            } catch (\Exception $e) {
            }
        }
        return null;
    }
}
