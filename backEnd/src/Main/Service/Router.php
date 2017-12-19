<?php

namespace Main\Service;

use Doctrine\ORM\OptimisticLockException;
use Main\Factory\ResponseFactory;
use Main\Service\Session\SessionManager;
use Main\Utils\AbstractSingleton;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

/**
 * @method static Router get()
 */
class Router extends AbstractSingleton
{
    const ROUTE_PARAM_CONTROLLER = '_controller';
    const ROUTE_PARAM_LOCALE = '_locale';

    protected static $inst;

    /** @var RouteCollection */
    protected $routes;
    protected $sitePath;
    /** @var RequestContext */
    protected $context;
    /** @var UrlGenerator */
    protected $urlGenerator;
    protected $requestParameters = [];

    public function init()
    {
        $this->context = new RequestContext('');
    }

    public function setRoutes(RouteCollection $routes)
    {
        $this->routes = $routes;
        $this->sitePath = isset($_GET['sitePath']) ? '/'.trim($_GET['sitePath'], ' ') : "/";
        $this->urlGenerator = new UrlGenerator($routes, $this->context);
    }

    public function getResponse()
    {
        $matcher = new UrlMatcher($this->routes, $this->context);
        try {
            $this->requestParameters = $matcher->match($this->sitePath);
            $controllerName = $this->requestParameters[self::ROUTE_PARAM_CONTROLLER];
            $parseRoute = explode(":", $controllerName);
            if (is_callable(array($parseRoute[0], $parseRoute[1]))) {
                if (SessionManager::get()->isLogged()) {
                    $user = SessionManager::get()->getLoggedUser();
                    if ($this->getRequestLocale() !== $user->getLang()) {
                        $user->setLang($this->getRequestLocale());
                        DB::get()->getEm()->persist($user);
                        try {
                            DB::get()->getEm()->flush();
                        } catch (OptimisticLockException $e) {
                        }
                    }
                }
                if ($_COOKIE['_locale'] !== $this->getRequestLocale()) {
                    setcookie('_locale', $this->getRequestLocale(), 0, '/');
                }
                $controller = new $parseRoute[0];
                return $controller->{$parseRoute[1]}();
            }
        } catch (ResourceNotFoundException $e) {
            return ResponseFactory::getSimpleResponse('NOT FOUND', 404);
        }

        foreach ($this->routes as $routRexExp => $method) {
            $routRexExp = '/^'.str_replace('/', '\/', $routRexExp).'$/';
            if (preg_match($routRexExp, $this->sitePath)) {
                $parseRoute = explode(":", $method);
                if (is_callable(array($parseRoute[0], $parseRoute[1]))) {
                    $controller = new $parseRoute[0];
                    return $controller->{$parseRoute[1]}();
                }
            }
        }
        return ResponseFactory::getSimpleResponse('NOT FOUND', 404);
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
