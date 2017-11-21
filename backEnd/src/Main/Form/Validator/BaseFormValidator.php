<?php

namespace Main\Form\Validator;

use Main\Form\AbstractDataValueManager;
use Main\Form\NullableTrait;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

abstract class BaseFormValidator extends AbstractDataValueManager implements ValidatorInterface
{
    use FormValidatorTrait,
        NullableTrait;

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    abstract protected function getDefaultErrorText();
    abstract protected function doCheck();

    /**
     * @param bool $nullable
     * @param string|LocalisationString|LocalisationChoiceString $customError
     */
    public function __construct(bool $nullable = false, $customError = '')
    {
        $this->setCustomError($customError);
        $this->setNullable($nullable);
    }

    public function check()
    {
        if (!($this->isNullable() && is_null($this->getValue()))) {
            $this->doCheck();
        }
    }

    /**
     * @return $this
     */
    public function bindError()
    {
        if ($this->customError !== '') {
            $this->setError($this->customError);
        } else {
            $this->setError($this->getDefaultErrorText());
        }
        return $this;
    }
}
