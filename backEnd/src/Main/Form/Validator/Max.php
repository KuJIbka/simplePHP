<?php

namespace Main\Form\Validator;

use Main\Exception\BaseException;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class Max extends BaseFormValidator
{
    protected $max;

    public function __construct($max, $customError = null, $value = null)
    {
        parent::__construct($customError, $value);
        if (!is_numeric($max)) {
            new BaseException('Min value of form validator must be numeric');
        }
        $this->setMax($max);
    }

    public function execute()
    {
        if ($this->getValue() > $this->getMax() || is_null($this->getValue())) {
            $this->bindError();
        }
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString('L_ERROR_NUMBER_MUST_BE_LOWER_THAN', [ '%max%' => $this->getMax() ]);
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function setMax($max)
    {
        $this->max = (float) $max;
        return $this;
    }
}
