<?php

namespace Main\Exception;

use Main\Exception\BaseException;
use Throwable;

class UserNotFound extends BaseException
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        $message = 'Такого пользователя не существует';
        parent::__construct($message, $code, $previous);
    }
}
