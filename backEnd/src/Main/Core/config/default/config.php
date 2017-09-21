<?php

use Main\Service\CacheDriver;
use Main\Service\SessionManager;

return [
    'db' => [
        [
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'user' => 'root',
            'port' => '3306',
            'password' => '',
            'dbname' => 'simple_php',
            'charset' => 'UTF8',
        ]
    ],
    'migrations' => [
        'dir' => PATH_ROOT.DIRECTORY_SEPARATOR.'Migrations',
        'namespace' => 'Main\\Migrations',
    ],
    'loginCountMax' => 3,
    'loginCountMaxTime' => 3600,
    'debug' => true,
    'language_default' => \Main\Service\TranslationsService::LANG_RU,
    'loginSecretKey' => 'K1ECMjIa1Bs0J5h3',

    'cache_driver' => CacheDriver::DRIVER_REDIS,

    'redis_host' => 'localhost',
    'redis_port' => 6379,
    'redis_timeout' => null,
    'redis_retryInterval' => 0,

    'session_save_handler' => SessionManager::SAVE_HANDLER_FILES,
    'session_save_path' => '',
];
