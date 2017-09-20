<?php

namespace Main\Filter;

abstract class BaseFilter
{
    abstract public function isEmpty();
    abstract public function clearFilters();

    protected $forUpdate = false;

    public function isForUpdate(): bool
    {
        return $this->forUpdate;
    }

    /**
     * @param bool $forUpdate
     * @return $this
     */
    public function setForUpdate(bool $forUpdate)
    {
        $this->forUpdate = $forUpdate;
        return $this;
    }
}
