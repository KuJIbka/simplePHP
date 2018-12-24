<?php

namespace Main\Controller;

use Main\Service\Session\SessionManager;
use Main\Service\traits\ConfigTrait;
use Main\Service\traits\RouterTrait;
use Main\Service\traits\SessionManagerTrait;
use Main\Service\traits\TemplaterTrait;
use Main\Service\traits\TranslationServiceTrait;

class BaseRenderController extends BaseController
{
    use ConfigTrait, TemplaterTrait, SessionManagerTrait, RouterTrait, TranslationServiceTrait;

    public function render($string, array $array = array()): string
    {
        $templater = $this->templater->getTemplater();
        $templater->addGlobal('_locale', $this->router->getRequestLocale());
        $csrfToken = $this->sessionManager->getParam(SessionManager::KEY_CSRF_TOKEN);
        $templater->addGlobal('csrf_token', $csrfToken);
        $templater->addGlobal('appConfig', json_encode(
            $this->config->getPublicSettings(),
            JSON_UNESCAPED_UNICODE
        ));
        try {
            return $templater->render($string, $array);
        } catch (\Exception $e) {
            $errorText = $this->translationService->getTranslator()->trans('L_COMMON_FATAL_ERROR');
            if ($this->config->getParam('debug')) {
                $errorText = $e->getMessage();
            }
            try {
                return $templater->render('error.html.twig', ['errorText' => $errorText]);
            } catch (\Exception $e) {
            }
        }
        return null;
    }
}
