<?php

namespace Main\Exception;

use Throwable;

class WrongData extends BaseFormDataException
{
    public function __construct(array $formsDataErrors = [], $code = 0, Throwable $previous = null)
    {
        $message = 'Неверные данные';
        parent::__construct($message, $formsDataErrors, $code, $previous);
    }
}
