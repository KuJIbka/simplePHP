<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class MaxLength extends BaseFormValidator
{
    protected $maxLength;

    public function __construct(int $maxLength, bool $nullable = false, $customError = null)
    {
        parent::__construct($nullable, $customError);
        $this->setMaxLength($maxLength);
    }

    public function doCheck()
    {
        if (mb_strlen($this->getValue()) > $this->getMaxLength() || is_null($this->getValue())) {
            $this->bindError();
        }
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString(
            'L_ERROR_STRING_MAX_LENGTH_MUST_BE_LOWER_THAN',
            [ '%maxLength%' => $this->getMaxLength() ]
        );
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function setMaxLength(int $maxLength): self
    {
        $this->maxLength = $maxLength;
        return $this;
    }
}
