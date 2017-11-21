<?php

namespace Main\Form\Converter;

use Main\Form\AbstractDataValueManager;

abstract class BaseConverter extends AbstractDataValueManager
{
    abstract public function convert();

    public function __construct($value = null)
    {
        $this->setValue($value);
    }
}
