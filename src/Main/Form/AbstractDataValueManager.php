<?php

namespace Main\Form;

abstract class AbstractDataValueManager implements DataManager
{
    protected $value;

    abstract public function execute();

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
