<?php

namespace Main\Service;

use Main\Utils\AbstractSingleton;
use Symfony\Bridge\Twig\Extension\TranslationExtension;

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
        $translator = TranslationsService::get()->getTranslator();
        $this->templater->addExtension(new TranslationExtension($translator));
    }

    public function getTemplater(): \Twig_Environment
    {
        return $this->templater;
    }
}
