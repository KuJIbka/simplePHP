<?php

namespace Main\Form\Data;

use Main\Form\AbstractFormData;
use Main\Form\Converter\ToInt;
use Main\Form\Converter\Trim;
use Main\Form\RuleContainer;
use Main\Form\Validator\Min;

class PaginationFormData extends AbstractFormData
{
    protected $length;
    protected $start;
    protected $orderBy;

    /** {@inheritdoc} */
    protected function getRules(): array
    {
        return [
            'length' => new RuleContainer([new Trim(), new ToInt(true), new Min(0, true)]),
            'start' => new RuleContainer([new Trim(), new ToInt(true), new Min(0, true)]),
            'orderBy' => new RuleContainer([new Trim()])
        ];
    }

    public function getLimit(): ?int
    {
        return $this->length;
    }

    public function getOffset(): ?int
    {
        return $this->start;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }
}
