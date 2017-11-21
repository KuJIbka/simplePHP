<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class Min extends BaseFormValidator
{
    protected $min;

    public function __construct(float $min, bool $nullable = false, $customError = null)
    {
        parent::__construct($nullable, $customError);
        $this->setMin($min);
    }

    public function check()
    {
        if ($this->getValue() < $this->getMin()) {
            $this->bindError();
        }
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_NUMBER_MUST_BE_GREATER_THAN', [ '%min%' => $this->getMin() ]);
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function setMin($min)
    {
        $this->min = (float) $min;
        return $this;
    }
}
