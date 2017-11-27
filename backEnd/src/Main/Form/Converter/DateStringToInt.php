<?php

namespace Main\Form\Converter;

class DateStringToInt extends BaseConverter
{
    public function doConvert()
    {
        if (preg_match('/^(\d\d)[-\/\.](\d\d)[-\/\.](\d\d\d\d)$/', $this->getValue(), $match)) {
            return mktime(0, 0, 0, $match[2], $match[1], $match[3]);
        }
        return null;
    }
}
