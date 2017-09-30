<?php

namespace Main\Exception;

use Throwable;

class WrongData extends BaseFormDataException
{
    public function __construct(array $formsDataErrors = [], $code = 0, Throwable $previous = null)
    {
        $message = 'L_ERROR_WRONG_DATA';
        parent::__construct($message, $formsDataErrors, $code, $previous);
    }
}
