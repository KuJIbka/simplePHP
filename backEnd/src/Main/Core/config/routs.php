<?php

use Main\Service\Config;
use Main\Service\Router;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/** @var Config $config */

$defaultLang = $config->getParam('language_default_lang');
$availableLangs = $config->getParam('language_available_langs');

# Global routes
$rootCollection = new RouteCollection();

$rootCollection->add(
    'setDefaultLang',
    new Route('/', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\MainController:setDefaultLang'])
);
$rootCollection->add(
    'getAppConfig',
    new Route('/getAppConfig', [Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\MainController:getAppConfig'])
);
#---------------

# Localised routes
$routes = new RouteCollection();
$routes->add(
    'main_page',
    new Route('', [
        Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\MainController:index',
        Router::ROUTE_CSRF_PROTECT => false,
    ])
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
        [ Router::ROUTE_PARAM_CONTROLLER => 'Main\Controller\AuthController:getUserSettings' ]
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
#---------------

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
