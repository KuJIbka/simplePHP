<?php

namespace Main\Exception;

use Throwable;

class CommonFatalError extends BaseException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = 'Что-то пошло не так, попробуй повторить операцию';
        parent::__construct($message, $code, $previous);
    }
}
