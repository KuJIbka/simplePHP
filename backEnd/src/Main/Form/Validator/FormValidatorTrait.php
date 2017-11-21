<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

trait FormValidatorTrait
{
    /**
     * @var string|LocalisationString|LocalisationChoiceString
     */
    protected $error;

    /**
     * @var string|LocalisationString|LocalisationChoiceString
     */
    protected $customError = '';

    /**
     * @param string|LocalisationString|LocalisationChoiceString $customError
     * @return $this
     */
    public function setCustomError($customError)
    {
        $this->customError = $customError;
        return $this;
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    public function getCustomError()
    {
        return $this->customError;
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string|LocalisationString|LocalisationChoiceString $error
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearError()
    {
        $this->error = null;
        return $this;
    }

    public function isValid(): bool
    {
        return is_null($this->error);
    }
}
