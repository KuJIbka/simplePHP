<?php

namespace Main\Form\Converter;

class Trim extends BaseConverter
{
    public function doConvert()
    {
        return is_null($this->getValue()) ? null : trim($this->getValue());
    }
}
