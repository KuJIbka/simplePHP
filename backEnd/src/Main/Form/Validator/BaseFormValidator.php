<?php

namespace Main\Form\Validator;

use Main\Form\AbstractDataValueManager;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

abstract class BaseFormValidator extends AbstractDataValueManager implements ValidatorInterface
{
    use FormValidatorTrait;

    /**
     * @var string|LocalisationString|LocalisationChoiceString
     */
    protected $error;

    /**
     * @var string|LocalisationString|LocalisationChoiceString
     */
    protected $customError = '';

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    abstract protected function getDefaultErrorText();

    /**
     * @param string|LocalisationString|LocalisationChoiceString $customError
     * @param mixed $value
     */
    public function __construct($customError = null, $value = null)
    {
        $this->setValue($value);
        $this->setCustomError($customError);
    }

    /**
     * {@inheritdoc}
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
