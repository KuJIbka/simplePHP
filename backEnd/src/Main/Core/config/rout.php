<?php

return [
    '/' => 'Main\Controller\MainController:index',
    '/auth/login' => "Main\Controller\AuthController:login",
    '/auth/logout' => "Main\Controller\AuthController:logout",
    '/in' => 'Main\Controller\MainController:in',

    '/testSession' => 'Main\Controller\TestController:testSession',
    '/testLockSession' => 'Main\Controller\TestController:testLockSession',
    '/testDB' => 'Main\Controller\TestController:testDB',
    '/testCache' => 'Main\Controller\TestController:testCache',
];
