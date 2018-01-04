<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class RegExpCheck extends BaseFormValidator
{
    private $pattern;

    public function __construct(string $regexp, bool $canBeEmpty = false, $customError = '')
    {
        parent::__construct($canBeEmpty, $customError);
        $this->pattern = $regexp;
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_REGEXP_VALIDATOR');
    }

    protected function doCheck()
    {
        if (is_null($this->getValue()) || !preg_match($this->pattern, $this->getValue())) {
            $this->bindError();
        }
        return $this->getValue();
    }
}
