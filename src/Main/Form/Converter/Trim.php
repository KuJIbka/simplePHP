<?php

namespace Main\Form\Converter;

class Trim extends BaseConverter
{
    public function execute()
    {
        return trim($this->getValue());
    }
}
