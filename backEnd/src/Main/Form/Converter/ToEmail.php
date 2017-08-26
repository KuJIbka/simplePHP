<?php

namespace Form\Converter;

use Main\Form\Converter\BaseConverter;

class ToEmail extends BaseConverter
{
    public function execute()
    {
        # Make not valid if using '
        $email = preg_replace("/['*!{}<>#%;:?=\/`~|]/", '"', $this->getValue());
        return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE);
    }
}
