<?php

use Main\Service\Config;
use Main\Service\Router;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$defaultLang = Config::get()->getParam('language_default_lang');
$availableLangs = Config::get()->getParam('language_available_langs');

$rootCollection = new RouteCollection();
$rootCollection->add(
    'setDefaultLang',
    new Route('/', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\MainController:setDefaultLang'])
);

$routes = new RouteCollection();
$routes->add(
    'main_page',
    new Route('', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\MainController:index'])
);
$routes->add(
    'user_login',
    new Route('/auth/login', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\AuthController:login'])
);
$routes->add(
    'user_logout',
    new Route('/auth/logout', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\AuthController:logout'])
);
$routes->add(
    'user_settings',
    new Route(
        '/auth/getUserSettings',
        [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\AuthController:getUserSettings']
    )
);
$routes->add(
    'user_in',
    new Route('/in', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\MainController:in'])
);

$routes->addPrefix(
    '/{_locale}',
    [Router::ROUTE_PARAM_LOCALE => $defaultLang],
    [Router::ROUTE_PARAM_LOCALE => implode('|', $availableLangs)]
);

if (Config::get()->getParam('debug')) {
    $rootCollection->add(
        'test_session',
        new Route('/testSession', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\TestController:testSession'])
    );
    $rootCollection->add(
        'test_db',
        new Route('/testDB', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\TestController:testDB'])
    );
    $rootCollection->add(
        'test_cache',
        new Route('/testCache', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\TestController:testCache'])
    );
    $rootCollection->add(
        'test_stepSess',
        new Route('/stepSess', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\TestController:stepSess'])
    );
}

$rootCollection->addCollection($routes);

return $rootCollection;

//
//return [
//    '/' => 'Main\Controller\MainController:index',
//    '/auth/login' => 'Main\Controller\AuthController:login',
//    '/auth/logout' => 'Main\Controller\AuthController:logout',
//    '/auth/getUserSettings' => 'Main\Controller\AuthController:getUserSettings',
//    '/in' => 'Main\Controller\MainController:in',
//
//    '/testSession' => 'Main\Controller\TestController:testSession',
//    '/testDB' => 'Main\Controller\TestController:testDB',
//    '/testCache' => 'Main\Controller\TestController:testCache',
//    '/stepSess' => 'Main\Controller\TestController:stepSess',
//];
