<?php

namespace Main\Form\Converter;

use Main\Form\AbstractDataValueManager;
use Main\Form\CanBeEmptyTrait;

abstract class BaseConverter extends AbstractDataValueManager
{
    use CanBeEmptyTrait;

    abstract protected function doConvert();

    public function __construct(bool $canBeEmpty = false)
    {
        $this->setCanBeEmpty($canBeEmpty);
    }

    public function convert()
    {
        if ($this->isCanBeEmpty() && (is_null($this->getValue()) || $this->getValue() === '')) {
            return null;
        }
        return $this->doConvert();
    }
}
