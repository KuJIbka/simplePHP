<?php

namespace Main\Service;

use Doctrine\ORM\EntityManager;
use Main\Controller\BaseController;
use Main\Core\AppContainer;
use Main\Events\RequestLifecycleBeforeMethodCall;
use Main\Factory\ResponseFactory;
use Main\Service\Session\SessionManager;
use Main\Struct\AppRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @method static Router get()
 */
class Router
{
    const ROUTE_PARAM_CONTROLLER = '_controller';
    const ROUTE_PARAM_LOCALE = '_locale';
    const ROUTE_CSRF_PROTECT = '_csrf_protect';

    protected static $inst;

    /** @var RouteCollection */
    protected $routes;
    protected $sitePath;
    /** @var RequestContext */
    protected $context;
    /** @var UrlGenerator */
    protected $urlGenerator;
    protected $requestParameters = [];

    /** @var AppContainer */
    protected $appContainer;
    /** @var SessionManager */
    protected $sessionManager;
    /** @var EntityManager */
    protected $entityManager;
    /** @var Config */
    protected $config;
    /** @var EventDispatcher  */
    protected $appEventDispatcher;
    /** @var ResponseFactory */
    protected $responseFactory;

    public function __construct(
        AppContainer $appContainer,
        SessionManager $sessionManager,
        EntityManager $entityManager,
        Config $config,
        EventDispatcher $appEventDispatcher,
        ResponseFactory $responseFactory
    ) {
        $this->context = new RequestContext('');
        $this->appContainer = $appContainer;
        $this->sessionManager = $sessionManager;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->appEventDispatcher = $appEventDispatcher;
        $this->responseFactory = $responseFactory;
    }

    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        $this->sitePath = isset($_GET['sitePath']) ? '/'.trim($_GET['sitePath'], ' ') : "/";
        $this->urlGenerator = new UrlGenerator($routes, $this->context);
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function getResponse()
    {
        $matcher = new UrlMatcher($this->routes, $this->context);
        try {
            $this->requestParameters = $matcher->match($this->sitePath);
            if (!isset($this->requestParameters[self::ROUTE_CSRF_PROTECT])) {
                $this->requestParameters[self::ROUTE_CSRF_PROTECT] = $this->config->getParam('csrf_protect_default');
            }
            $controllerName = $this->requestParameters[self::ROUTE_PARAM_CONTROLLER];
            $parseRoute = explode(":", $controllerName);
            if (is_callable([$parseRoute[0], $parseRoute[1]])) {
                $request = AppRequest::createFromGlobals();
                $request->setRouterParameters(new ParameterBag($this->requestParameters));
                $request->setSessionManager($this->sessionManager);
                $request->setDefaultLocale($this->config->getParam('language_default_lang'));

                $locale = isset($this->requestParameters[self::ROUTE_PARAM_LOCALE])
                    ? $this->requestParameters[self::ROUTE_PARAM_LOCALE]
                    : '';
                if (!$locale && isset($_COOKIE['_locale']) && $_COOKIE['_locale']) {
                    $locale = $_COOKIE['_locale'];
                }

                $request->setLocale($locale);
                if (isset($_COOKIE['_locale']) && $_COOKIE['_locale'] !== $locale) {
                    setcookie('_locale', $this->getRequestLocale(), 0, '/');
                }

                $event = new RequestLifecycleBeforeMethodCall($request);
                $this->appEventDispatcher->dispatch(
                    RequestLifecycleBeforeMethodCall::class,
                    $event
                );
                $response = $event->getResponse();
                if (!$response) {
                    /** @var BaseController $controller */
                    $controller = $this->appContainer->get($parseRoute[0]);
                    $controller->setAppRequest($request);
                    $response = $controller->{$parseRoute[1]}();
                }
                return $response;
            }
        } catch (ResourceNotFoundException $e) {
            return $this->responseFactory->getSimpleResponse('NOT FOUND', 404);
        }
        return $this->responseFactory->getSimpleResponse('NOT FOUND', 404);
    }

    public function getUrlGenerator(): ?UrlGenerator
    {
        return $this->urlGenerator;
    }

    public function getRequestParameters(): array
    {
        return $this->requestParameters;
    }

    public function getRequestLocale(): ?string
    {
        return $this->requestParameters[self::ROUTE_PARAM_LOCALE] ?? null;
    }
}
