<?php

namespace Main\Form\Validator;

/**
 * @method string getDefaultErrorText()
 */
trait FormValidatorTrait
{
    protected $error;
    protected $customError = '';

    public function setCustomError($customError, $args = [])
    {
        $this->customError = $customError;
        return $this;
    }

    public function getCustomError()
    {
        return $this->customError;
    }

    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    public function isValid()
    {
        return is_null($this->error);
    }

    public function getError()
    {
        return $this->error;
    }

    public function clearError()
    {
        $this->error = null;
        return $this;
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
