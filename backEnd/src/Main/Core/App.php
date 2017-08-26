<?php

namespace Main\Core;

use Main\Service\Config;

abstract class App
{
    public function __construct()
    {
        require_once __DIR__ . '/defines.php';
        Config::get()->loadFromPath(PATH_CONFIG.'/default');
        Config::get()->loadFromPath(PATH_CONFIG.'/prod');

        if (Config::get()->getParam('debug')) {
            ini_set('error_reporting', E_ALL);
            ini_set("display_startup_errors", "1");
            ini_set('display_errors', 1);
        }

        if (!isset($_SERVER['REQUEST_TIME'])) {
            $_SERVER['REQUEST_TIME'] = time();
        }
    }

    abstract public function run();
}
