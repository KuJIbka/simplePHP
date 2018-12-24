<?php

namespace Main\Service\traits;

use Main\Service\Templater;

trait TemplaterTrait
{
    /** @var Templater */
    protected $templater;

    public function setTemplater(Templater $templater)
    {
        $this->templater = $templater;
    }
}
