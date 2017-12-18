<?php

namespace Main\Controller;

use Main\Service\Router;
use Main\Service\Templater;

class BaseController
{
    public function render($string, array $array = array()): string
    {
        Templater::get()->getTemplater()->addGlobal('_locale', Router::get()->getRequestLocale());
        return Templater::get()->getTemplater()->render($string, $array);
    }
}
