<?php

namespace Main\Struct;

class PaginationData implements \JsonSerializable
{
    protected $data;
    protected $recordsTotal;
    protected $recordsFiltered;

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getRecordsTotal(): int
    {
        return $this->recordsTotal;
    }

    public function setRecordsTotal(int $recordsTotal): self
    {
        $this->recordsTotal = $recordsTotal;
        return $this;
    }

    public function getRecordsFiltered(): int
    {
        return $this->recordsFiltered;
    }

    public function setRecordsFiltered(int $recordsFiltered): self
    {
        $this->recordsFiltered = $recordsFiltered;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'data' => $this->getData(),
            'recordsTotal' => $this->getRecordsTotal(),
            'recordsFiltered' => $this->getRecordsFiltered(),
        ];
    }
}
