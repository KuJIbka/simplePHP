<?php

namespace Main\Form\Validator;

use Main\Service\Utils;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class Max extends BaseFormValidator
{
    protected $max;

    public function __construct(float $max = null, bool $nullable = false, $customError = null)
    {
        parent::__construct($nullable, $customError);
        $this->setMax($max);
    }

    public function doCheck()
    {
        if (is_null($this->getValue()) || Utils::get()->compareFloat($this->getValue(), $this->getMax()) === 1) {
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
