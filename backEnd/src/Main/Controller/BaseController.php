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
        Templater::get()->getTemplater()->addGlobal('_locale', Router::get()->getRequestLocale());
        $csrfToken = SessionManager::get()->getParam(SessionManager::KEY_CSRF_TOKEN);
        Templater::get()->getTemplater()->addGlobal('csrf_token', $csrfToken);
        try {
            return Templater::get()->getTemplater()->render($string, $array);
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
