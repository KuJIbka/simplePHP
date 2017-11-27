<?php

use Main\Service\CacheDriver;
use Main\Service\Session\SessionManager;

return [
    'db_driver' => 'pdo_mysql',
    'db_host' => 'localhost',
    'db_port' => 3306,
    'db_dbname' => 'simple_php',
    'db_charset' => 'UTF8',
    'db_user' => 'root',
    'db_password' => '',

    'migrations' => [
        'dir' => PATH_ROOT.DS.'Migrations',
        'namespace' => 'Main\\Migrations',
    ],
    'loginCountMax' => 3,
    'loginCountMaxTime' => 3600,
    'debug' => true,
    'language_default' => \Main\Service\TranslationsService::LANG_RU,
    'loginSecretKey' => 'K1ECMjIa1Bs0J5h3',

    'cache_driver' => CacheDriver::DRIVER_ARRAY,
    'cache_namespace' => 'main_app',
    'cache_lock_try_timeout' => 10000,
    'cache_lock_expire' => 600,

    'redis_host' => 'localhost',
    'redis_port' => 6379,
    'redis_timeout' => null,
    'redis_retryInterval' => 0,

    'session_save_handler' => SessionManager::SAVE_HANDLER_FILES,
    'session_save_path' => '',
    'session_lifetime' => 1440,
    'session_max_lock_time' => 15,
];
