<?php

namespace Main\Form;

trait NullableTrait
{
    protected $nullable = false;

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     * @return $this
     */
    public function setNullable(bool $nullable)
    {
        $this->nullable = $nullable;
        return $this;
    }
}
