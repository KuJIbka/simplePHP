<?php

namespace Main\Exception;

use Throwable;

class BaseFormDataException extends BaseException
{
    protected $formDataErrors = [];

    public function __construct(
        $message = 'L_ERROR_WRONG_DATA',
        array $formsDataErrors = [],
        $code = 0,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->setFormDataErrors($formsDataErrors);
    }

    public function getFormDataErrors(): array
    {
        return $this->formDataErrors;
    }

    public function setFormDataErrors(array $formDataErrors): self
    {
        $this->formDataErrors = $formDataErrors;
        return $this;
    }
}
