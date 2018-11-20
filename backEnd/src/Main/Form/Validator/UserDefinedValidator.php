<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class UserDefinedValidator extends BaseFormValidator
{
    public $userValidator;

    public function __construct(\Closure $closure, bool $canBeEmpty = false, $customError = '')
    {
        parent::__construct($canBeEmpty, $customError);
        $this->userValidator = $closure;
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_WRONG_DATA');
    }

    protected function doCheck()
    {
        return \call_user_func($this->userValidator, $this->getValue(), $this);
    }
}
