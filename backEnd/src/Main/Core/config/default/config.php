<?php

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
    'loginSecretKey' => 'K1ECMjIa1Bs0J5h3',
];
