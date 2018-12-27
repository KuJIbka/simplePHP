<?php

use Doctrine\ORM\EntityManager;
use Main\Command\TestCommand;
use Main\Command\AddNewUserCommand;
use Main\Command\InitCacheTagsCommand;
use Main\Command\LangToJsonCommand;
use Main\Controller\AuthController;
use Main\Controller\MainController;
use Main\Core\AppContainer;
use Main\Entity\Permission;
use Main\Entity\Role;
use Main\Entity\User;
use Main\Entity\UserLimit;
use Main\Events\RequestLifecycleBeforeMethodCall;
use Main\Factory\ResponseFactory;
use Main\Listeners\CsrfTokenListener;
use Main\Listeners\RequestLifecycleListener;
use Main\Repository\PermissionRepository;
use Main\Repository\RoleRepository;
use Main\Repository\UserLimitRepository;
use Main\Repository\UserRepository;
use Main\Service\AppEventDispatcher;
use Main\Service\CacheDriver;
use Main\Service\Config;
use Main\Service\DB;
use Main\Service\PermissionService;
use Main\Service\Router;
use Main\Service\Session\SessionManager;
use Main\Service\Templater;
use Main\Service\TranslationService;
use Main\Service\UserLimitService;
use Main\Service\UserService;
use Main\Service\Utils;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/** @var AppContainer $appContainer */

$container = $appContainer->getRawContainer();
$container->register(Config::class, Config::class)->setPublic(true);

# --------- Events ---------
$container->addCompilerPass(new RegisterListenersPass(
    AppEventDispatcher::class,
    AppContainer::TAG_APP_EVENT_LISTENER,
    AppContainer::TAG_APP_EVENT_SUBSCRIBER
));

$container->register(CsrfTokenListener::class, CsrfTokenListener::class)
    ->addMethodCall('setSessionManager', [ new Reference(SessionManager::class) ])
    ->addMethodCall('setResponseFactory', [ new Reference(ResponseFactory::class) ])
    ->addTag(
        AppContainer::TAG_APP_EVENT_LISTENER,
        [
            'event' => RequestLifecycleBeforeMethodCall::class,
            'method' => 'onBeforeMethodCall'
        ]
    );

$container->register(RequestLifecycleListener::class, RequestLifecycleListener::class)
    ->addMethodCall('setSessionManager', [ new Reference(SessionManager::class) ])
    ->addMethodCall('setEntityManager', [ new Reference(EntityManager::class) ])
    ->addTag(
        AppContainer::TAG_APP_EVENT_LISTENER,
        [
            'event' => RequestLifecycleBeforeMethodCall::class,
            'method' => 'onBeforeMethodCall'
        ]
    );

$container->register(AppEventDispatcher::class, AppEventDispatcher::class)->setPublic(true);
# --------- END Events ---------


# --------- Services ---------
$container->register(CacheDriver::class, CacheDriver::class)
    ->addArgument(new Reference(Config::class));

$container->register(SessionManager::class, SessionManager::class)
    ->addArgument(new Reference(Config::class))
    ->addArgument(new Reference(UserRepository::class))
    ->addArgument(new Reference(RoleRepository::class))
    ->setLazy(true)
    ->setPublic(true)
;

$container->register(Router::class, Router::class)
    ->addArgument($appContainer)
    ->addArgument(new Reference(SessionManager::class))
    ->addArgument(new Reference(EntityManager::class))
    ->addArgument(new Reference(Config::class))
    ->addArgument(new Reference(AppEventDispatcher::class))
    ->addArgument(new Reference(ResponseFactory::class))
    ->setPublic(true)
;

$container->register(TranslationService::class, TranslationService::class)
    ->addArgument(new Reference(Config::class))
    ->setLazy(true)
    ->setPublic(true)
;

$container->register(ResponseFactory::class, ResponseFactory::class)
    ->addArgument(new Reference(TranslationService::class));

$container->register(DB::class, DB::class)
    ->addArgument(new Reference(Config::class))
    ->addArgument(new Reference(CacheDriver::class))
    ->setLazy(true)
    ->setPublic(true)
;

$container->register(EntityManager::class)
    ->setFactory([new Reference(DB::class), 'getEm'])
    ->setLazy(true)
    ->setPublic(true)
;

$container->register(Utils::class, Utils::class)->setPublic(true);

$container->register(UserService::class, UserService::class)
    ->addArgument(new Reference(EntityManager::class))
    ->setLazy(true);
$container->register(UserLimitService::class, UserLimitService::class)
    ->addArgument(new Reference(Config::class))
    ->setLazy(true);

$container->register(Templater::class, Templater::class)
    ->addArgument(new Reference(TranslationService::class));

$container->register(PermissionService::class, PermissionService::class)
    ->addArgument(new Reference(CacheDriver::class))
    ->addArgument(new Reference(RoleRepository::class))
    ->setLazy(true);
# --------- END Services ---------


# --------- Controllers ---------
$bindBaseController = function (Definition $definition) use ($appContainer) {
    $definition->addMethodCall('setResponseFactory', [ new Reference(ResponseFactory::class) ])
        ->addMethodCall('setSessionManager', [ new Reference(SessionManager::class) ])
        ->addMethodCall('setPermissionService', [ new Reference(PermissionService::class) ])
        ->addMethodCall('setEntityManager', [ new Reference(EntityManager::class) ])
        ->setPublic(true)
    ;
};

$bindRenderController = function (Definition $definition) use ($appContainer, $bindBaseController) {
    $bindBaseController($definition);
    $definition->addMethodCall('setConfig', [ new Reference(Config::class) ])
        ->addMethodCall('setTemplater', [ new Reference(Templater::class) ])
        ->addMethodCall('setRouter', [ new Reference(Router::class) ])
        ->addMethodCall('setTranslationService', [ new Reference(TranslationService::class) ])
    ;
};

$authControllerDefinition = $container->register(AuthController::class, AuthController::class)
    ->addMethodCall('setUserLimitService', [new Reference(UserLimitService::class)])
    ->addMethodCall('setUserLimitRepository', [new Reference(UserLimitRepository::class)])
    ->addMethodCall('setPermissionService', [new Reference(PermissionService::class)])
    ->addMethodCall('setEntityManager', [new Reference(EntityManager::class)])
    ->addMethodCall('setUserRepository', [new Reference(UserRepository::class)])
    ->addMethodCall('setRouter', [new Reference(Router::class)])
;


$bindBaseController($authControllerDefinition);

$MainControllerDefinition = $container->register(MainController::class, MainController::class);
$bindRenderController($MainControllerDefinition);

# --------- END Controllers ---------


# --------- Repository Services ---------
$container->register(UserRepository::class)
    ->addArgument(User::class)
    ->setFactory([new Reference(EntityManager::class), 'getRepository'])
    ->setLazy(true);

$container->register(UserLimitRepository::class)
    ->addArgument(UserLimit::class)
    ->setFactory([new Reference(EntityManager::class), 'getRepository'])
    ->setLazy(true);

$container->register(PermissionRepository::class)
    ->addArgument(Permission::class)
    ->setFactory([new Reference(EntityManager::class), 'getRepository'])
    ->setLazy(true);

$container->register(RoleRepository::class)
    ->addArgument(Role::class)
    ->setFactory([new Reference(EntityManager::class), 'getRepository'])
    ->setLazy(true);
# --------- END Repository Services ---------


# --------- Commands ---------
$container->register(TestCommand::class, TestCommand::class)
    ->addMethodCall('setAppContainer', [$appContainer])
    ->setPublic(true)
;

$container->register(LangToJsonCommand::class, LangToJsonCommand::class)
    ->addMethodCall('setTranslationService', [new Reference(TranslationService::class)])
    ->setPublic(true)
;

$container->register(AddNewUserCommand::class, AddNewUserCommand::class)
    ->addMethodCall('setUserService', [new Reference(UserService::class)])
    ->setPublic(true)
;

$container->register(InitCacheTagsCommand::class, InitCacheTagsCommand::class)
    ->addMethodCall('setCacheDriver', [new Reference(CacheDriver::class)])
    ->setPublic(true)
;
# --------- END Commands ---------
