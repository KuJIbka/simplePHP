<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class Email extends BaseFormValidator
{
    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_WRONG_EMAIL_FORMAT');
    }

    protected function doCheck()
    {
        if (!filter_var($this->getValue(), FILTER_VALIDATE_EMAIL)) {
            $this->bindError();
        }
    }
}
