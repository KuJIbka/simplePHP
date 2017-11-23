<?php

namespace Main\Form\Converter;

class ToEmail extends BaseConverter
{
    public function doConvert()
    {
        # Make not valid if using '
        $email = preg_replace("/['*!{}<>#%;:?=\/`~|]/", '"', $this->getValue());
        return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
    }
}
