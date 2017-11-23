<?php

namespace Main\Form\Converter;

class Trim extends BaseConverter
{
    public function doConvert()
    {
        return trim($this->getValue());
    }
}
