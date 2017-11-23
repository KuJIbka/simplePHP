<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class DateFormatString extends BaseFormValidator
{
    public function doCheck()
    {
        if (!preg_match('/^\d\d[-\/\.]\d\d[-\/\.]\d\d\d\d$/', $this->getValue())) {
            $this->bindError();
        }
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_WRONG_DATE_FORMAT');
    }
}
