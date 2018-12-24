<?php

namespace Main\Service;

use Symfony\Bridge\Twig\Extension\TranslationExtension;

class Templater
{
    protected static $inst;
    /** @var TranslationService */
    protected $translationsService;
    private $templater;

    public function __construct(TranslationService $translationsService)
    {
        $this->translationsService = $translationsService;

        $loader = new \Twig_Loader_Filesystem(PATH_ROOT.'/Template/');
        $this->templater = new \Twig_Environment($loader);
        $translator = $this->translationsService->getTranslator();
        $this->templater->addExtension(new TranslationExtension($translator));
    }

    public function getTemplater(): \Twig_Environment
    {
        return $this->templater;
    }
}
