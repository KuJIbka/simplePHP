<?php

return [
    '/' => 'Main\Controller\MainController:index',
    '/auth/login' => "Main\Controller\AuthController:login",
    '/auth/logout' => "Main\Controller\AuthController:logout",
    '/in' => 'Main\Controller\MainController:in',

    '/testCache' => 'Main\Controller\TestController:testCache',
];
