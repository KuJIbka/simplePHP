<?php

namespace Main\Core;

use Main\Service\Config;

abstract class App
{
    /** @var AppContainer */
    protected $appContainer;
    /** @var Config */
    protected $config;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        ini_set('precision', 14);
        ini_set('serialize_precision', -1);

        if (!isset($_SERVER['REQUEST_TIME'])) {
            $_SERVER['REQUEST_TIME'] = time();
        }
        $_SERVER['REQUEST_TIME_MICRO'] = microtime(true);

        require_once __DIR__ . '/defines.php';

        $this->appContainer = new AppContainer();

        $appContainer = $this->appContainer;
        $containerConfigPath = PATH_CONFIG.DS.'services.php';

        if (file_exists($containerConfigPath)) {
            $loadServices = function () use ($containerConfigPath, $appContainer) {
                require_once $containerConfigPath;
            };
            $loadServices();
        }
        $appContainer->getRawContainer()->compile();

        $this->config = $this->appContainer->getConfig();
        $this->config->loadFromPath(PATH_CONFIG.'/default');
        $this->config->loadFromPath(PATH_CONFIG.'/prod');

        if ($this->config->getParam('debug')) {
            ini_set('error_reporting', E_ALL);
            ini_set("display_startup_errors", "1");
            ini_set('display_errors', 1);
        }
    }

    abstract public function run();
}
