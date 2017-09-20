<?php

namespace Main\Filter;

class UserFilter extends BaseFilter
{
    protected $ids;

    public function getIds()
    {
        return $this->ids;
    }

    public function setIds(array $ids = null)
    {
        $this->ids = $ids;
        return $this;
    }

    public function isEmpty()
    {
        return (!is_null($this->getIds()) && empty($this->getIds()));
    }

    public function clearFilters()
    {
        $this->setIds(null);
    }
}
