<?php

return [
    'db' => [
        [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'dbName' => 'simple_php'
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
