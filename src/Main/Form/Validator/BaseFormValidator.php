<?php

namespace Main\Form\Validator;

use Main\Form\AbstractDataValueManager;

abstract class BaseFormValidator extends AbstractDataValueManager implements ValidatorInterface
{
    use FormValidatorTrait;

    protected $error;
    protected $customError = '';

    abstract protected function getDefaultErrorText():string;

    public function __construct($customError = null, $value = null)
    {
        $this->setValue($value);
        $this->setCustomError($customError);
    }

    public function bindError()
    {
        if ($this->customError !== '') {
            $this->setError($this->customError);
        } else {
            $this->setError($this->getDefaultErrorText());
        }
    }
}
