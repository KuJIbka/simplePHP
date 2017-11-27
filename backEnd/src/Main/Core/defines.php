<?php

define('DS', DIRECTORY_SEPARATOR);
define(
    "PATH_PROJECT_ROOT",
    dirname(dirname(dirname(dirname(__DIR__))))
);
define("PATH_BACKEND", PATH_PROJECT_ROOT.DS.'backEnd');
define("PATH_ROOT", PATH_BACKEND.DS.'src'.DS.'Main');
define(
    "PATH_CONFIG",
    PATH_ROOT.
    DS.'Core'.
    DS.'config'
);
define("PATH_LANGS", PATH_ROOT.DS.'Lang');
define("PATH_PUBLIC", PATH_PROJECT_ROOT.DS.'web');
