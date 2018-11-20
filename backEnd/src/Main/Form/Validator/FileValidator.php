<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class FileValidator extends BaseFormValidator
{
    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_WRONG_DATA');
    }

    protected function doCheck()
    {
        $value = $this->getValue();
        if (isset($value['error']) && $value['error'] !== UPLOAD_ERR_OK) {
            if (!$this->customError) {
                $this->setError(new LocalisationString('L_ERROR_WRONG_FILE_'.$value['error']));
            } else {
                $this->bindError();
            }
        }
    }
}
