<?php

namespace Main\Form\Converter;

use Main\Form\AbstractDataValueManager;
use Main\Form\NullableTrait;

abstract class BaseConverter extends AbstractDataValueManager
{
    use NullableTrait;

    abstract protected function doConvert();

    public function __construct(bool $nullable = false)
    {
        $this->setNullable($nullable);
    }

    public function convert()
    {
        if ($this->isNullable() && is_null($this->getValue())) {
            return null;
        }
        return $this->doConvert();
    }
}
