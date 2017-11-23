<?php

namespace Main\Form\Validator;

use Main\Form\AbstractDataValueManager;
use Main\Form\CanBeEmptyTrait;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

abstract class BaseFormValidator extends AbstractDataValueManager implements ValidatorInterface
{
    use FormValidatorTrait,
        CanBeEmptyTrait;

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    abstract protected function getDefaultErrorText();
    abstract protected function doCheck();

    /**
     * @param bool $canBeEmpty
     * @param string|LocalisationString|LocalisationChoiceString $customError
     */
    public function __construct(bool $canBeEmpty = false, $customError = '')
    {
        $this->setCustomError($customError);
        $this->setCanBeEmpty($canBeEmpty);
    }

    public function check()
    {
        if (!($this->isCanBeEmpty() && (is_null($this->getValue()) || $this->getValue() === ''))) {
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
