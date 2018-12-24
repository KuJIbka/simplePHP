<?php

namespace Main\Listeners;

use Main\Events\RequestLifecycleBeforeMethodCall;
use Main\Factory\ResponseFactory;
use Main\Factory\traits\ResponseFactoryTrait;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Main\Service\traits\SessionManagerTrait;
use Main\Struct\LocalisationString;

class CsrfTokenListener
{
    use SessionManagerTrait,
        ResponseFactoryTrait;

    public function onBeforeMethodCall(RequestLifecycleBeforeMethodCall $event)
    {
        $request = $event->getAppRequest();
        if ($request->getRouterParameters()->get(Router::ROUTE_CSRF_PROTECT)) {
            $csrfTokenFromSession = $this->sessionManager->getParam(SessionManager::KEY_CSRF_TOKEN);
            if ($csrfTokenFromSession) {
                $method = $event->getAppRequest()->getMethod();
                $csrfToken = null;
                if ($method === 'GET') {
                    $csrfToken = $event->getAppRequest()->query->get('csrf_token');
                } elseif ($method === 'POST') {
                    $csrfToken = $event->getAppRequest()->request->get('csrf_token');
                }
                if ($csrfToken !== $csrfTokenFromSession) {
                    $body = $this->responseFactory->getDefaultResponseData(
                        ResponseFactory::RESP_TYPE_ERROR,
                        new LocalisationString('L_ERROR_CSRF_TOKEN')
                    );
                    $response = $this->responseFactory->getJsonResponse($body);
                    $event->setResponse($response);
                }
            }
        }
    }
}
