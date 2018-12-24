<?php

namespace Main\Core;

use Main\Exception\BaseException;
use Main\Exception\CommonFatalError;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Symfony\Component\HttpFoundation\Response;

class AppHttp extends App
{
    /** @var Router */
    protected $router;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $sessionManager = $this->appContainer->getSessionManager();
        $sessionManager->open();
        if (!$sessionManager->issetParam(SessionManager::KEY_CSRF_TOKEN)) {
            $sessionManager->setParam(
                SessionManager::KEY_CSRF_TOKEN,
                $this->appContainer->getUtils()->generateCode(32)
            );
        }
        $sessionManager->close();
        $this->router = $this->appContainer->getRouter();
        $routesPath = PATH_CONFIG.DS.'routs.php';
        $config = $this->config;
        $router = $this->router;
        if (file_exists($routesPath)) {
            $loadRoutes = function () use ($config, $router, $routesPath) {
                $router->setRoutes(require_once $routesPath);
            };
            $loadRoutes();
        }
    }

    public function run()
    {
        $em = $this->appContainer->getEm();
        $responseFactory = $this->appContainer->getResponseFactory();
        try {
            /** @var Response|string $response */
            $response = $this->router->getResponse();
            if ($response instanceof Response) {
                $response->send();
            } else {
                echo $response;
            }
        } catch (\Exception $e) {
            $em->getConnection()->isTransactionActive() && $em->rollback();
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']);
            if ($e instanceof BaseException) {
                $response = $responseFactory->exceptionToResponse($e, $isAjax);
            } else {
                $errorMessage = explode('Stack trace', $e->getMessage())[0];
                if (!trim($errorMessage)) {
                    $errorMessage = $e->getMessage();
                }
                error_log('Error: "' . $errorMessage . '" in ' . $e->getFile() . ':' . $e->getLine());
                error_log($e->getMessage());
                $first = true;
                foreach ($e->getTrace() as $k => $trace) {
                    $class = isset($trace['class']) ? $trace['class'] : '';
                    $type = isset($trace['type']) ? $trace['type'] : '';
                    $function = isset($trace['function']) ? $trace['function'] : '';
                    $file = isset($trace['file']) ? $trace['file'] : '';
                    $line = isset($trace['line']) ? $trace['line'] : '';
                    if ($first) {
                        error_log('Stack Trace: ');
                        $first = false;
                    }
                    error_log(' ' . $k . '. ' . $class . $type . $function . '()' . ' ' . $file . ':' . $line);
                }
                if ($this->config->getParam('debug')) {
                    $response = $responseFactory->getSimpleResponse();
                    $respString = $e->getMessage();
                    $respString .= "<br />\n".$e->getFile().'::'.$e->getLine();
                    $response->setContent($respString);
                } else {
                    $commonFatalError = new CommonFatalError();
                    $response = $responseFactory->exceptionToResponse($commonFatalError, $isAjax);
                }
            }
            $response->send();
        }
    }
}
