<?php

namespace Form\Converter;

use Main\Form\Converter\BaseConverter;

class ToFloat extends BaseConverter
{
    public function execute()
    {
        $value = str_replace(',', '.', $this->getValue());
        return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
    }
}
