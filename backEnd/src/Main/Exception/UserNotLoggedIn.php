<?php

namespace Main\Exception;

use Throwable;

class UserNotLoggedIn extends BaseException
{
    public function __construct($message = "L_ERROR_USER_NOT_LOGGED_IN", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
