<?php

namespace Main\Utils;

abstract class AbstractSingleton
{
    protected static $inst;

    private function __construct()
    {
        $this->init();
    }

    public static function get()
    {
        if (is_null(static::$inst)) {
            $class = get_called_class();
            static::$inst = new $class();
        }
        return static::$inst;
    }

    protected function init()
    {
    }
}
