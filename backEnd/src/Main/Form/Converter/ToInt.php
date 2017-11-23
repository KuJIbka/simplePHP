<?php

namespace Main\Form\Converter;

class ToInt extends BaseConverter
{
    public function doConvert()
    {
        return filter_var($this->value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
    }
}
