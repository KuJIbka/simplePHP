<?php

namespace Main\Core;

use Main\Exception\BaseException;
use Main\Exception\CommonFatalError;
use Main\Factory\ResponseFactory;
use Main\Service\Config;
use Main\Service\DB;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Main\Service\TranslationsService;
use Main\Service\Utils;
use Sabre\HTTP\Response;
use Sabre\HTTP\Sapi;

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
        $sessionManager = SessionManager::get();
        $sessionManager->open();
        if (!$sessionManager->issetParam(SessionManager::KEY_CSRF_TOKEN)) {
            $sessionManager->setParam(SessionManager::KEY_CSRF_TOKEN, Utils::get()->generateCode(32));
        }
        $sessionManager->close();
        $this->router = Router::get();
        $this->router->setRoutes(require_once PATH_CONFIG.'/rout.php');
    }

    public function run()
    {
        try {
            /** @var Response|string $response */
            $response = $this->router->getResponse();
            if ($response instanceof Response) {
                Sapi::sendResponse($response);
            } else {
                echo $response;
            }
        } catch (\Exception $e) {
            DB::get()->getEm()->getConnection()->isTransactionActive() && DB::get()->getEm()->rollback();
            $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']);
            if ($e instanceof BaseException) {
                $response = ResponseFactory::exceptionToResponse($e, $isAjax);
            } else {
                $response = ResponseFactory::getSimpleResponse();
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
                if (Config::get()->getParam('debug')) {
                    $respString = $e->getMessage();
                    $respString .= "<br />\n".$e->getFile().'::'.$e->getLine();
                    $response->setBody($respString);
                } else {
                    $commonFatalError = new CommonFatalError();
                    $commonFatalErrorText = TranslationsService::get()
                        ->getTranslator()
                        ->trans($commonFatalError->getMessage());
                    $response->setBody($commonFatalErrorText);
                }
            }
            Sapi::sendResponse($response);
        }
    }
}
