<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationString;

class NotBlank extends BaseFormValidator
{
    public function execute()
    {
        if (is_null($this->getValue()) || $this->getValue() === '') {
            $this->bindError();
        }
        return $this->getValue();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_FIELD_CAN_NOT_BE_EMPTY');
    }
}
