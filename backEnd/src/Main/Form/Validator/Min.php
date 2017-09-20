<?php

namespace Main\Form\Validator;

use Main\Exception\BaseException;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class Min extends BaseFormValidator
{
    protected $min;

    public function __construct($min, $customError = null, $value = null)
    {
        parent::__construct($customError, $value);
        if (!is_numeric($min)) {
            new BaseException('Min value of form validator must be numeric');
        }
        $this->setMin($min);
    }

    public function execute()
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
