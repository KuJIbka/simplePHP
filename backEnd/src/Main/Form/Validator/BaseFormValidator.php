<?php

namespace Main\Form\Validator;

use Main\Form\AbstractDataValueManager;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

abstract class BaseFormValidator extends AbstractDataValueManager implements ValidatorInterface
{
    use FormValidatorTrait;

    protected $nullable = false;

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    abstract protected function getDefaultErrorText();
    abstract protected function check();

    /**
     * @param bool $nullable
     * @param string|LocalisationString|LocalisationChoiceString $customError
     */
    public function __construct(bool $nullable = false, $customError = '')
    {
        $this->setCustomError($customError);
        $this->setNullable($nullable);
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     * @return $this
     */
    public function setNullable(bool $nullable)
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function process()
    {
        if (!($this->isNullable() && is_null($this->getValue()))) {
            $this->check();
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
