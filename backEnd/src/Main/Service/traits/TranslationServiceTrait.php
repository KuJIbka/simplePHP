<?php

namespace Main\Service\traits;

use Main\Service\TranslationService;

trait TranslationServiceTrait
{
    /** @var TranslationService */
    protected $translationService;

    public function setTranslationService(TranslationService $service)
    {
        $this->translationService = $service;
    }
}
