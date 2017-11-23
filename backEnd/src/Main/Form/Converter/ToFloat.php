<?php

namespace Main\Form\Converter;

class ToFloat extends BaseConverter
{
    public function doConvert()
    {
        $value = str_replace(',', '.', $this->getValue());
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }
}
