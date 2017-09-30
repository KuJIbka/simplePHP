<?php

namespace Main\Exception;

use Throwable;

class UserNotFound extends BaseException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = 'L_ERROR_USER_NOT_FOUND';
        parent::__construct($message, $code, $previous);
    }
}
