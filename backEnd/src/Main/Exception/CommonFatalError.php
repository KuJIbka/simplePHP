<?php

namespace Main\Exception;

use Throwable;

class CommonFatalError extends BaseException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = 'L_COMMON_FATAL_ERROR';
        parent::__construct($message, $code, $previous);
    }
}
