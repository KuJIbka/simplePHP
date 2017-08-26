<?php

namespace Main\Service;

use Main\Utils\AbstractSingleton;

/**
 * @method static Templater get()
 */
class Templater extends AbstractSingleton
{
    protected static $inst;
    private $templater;

    protected function init()
    {
        $loader = new \Twig_Loader_Filesystem(PATH_ROOT.'/Template/');
        $this->templater = new \Twig_Environment($loader);
    }

    public function getTemplater(): \Twig_Environment
    {
        return $this->templater;
    }
}
