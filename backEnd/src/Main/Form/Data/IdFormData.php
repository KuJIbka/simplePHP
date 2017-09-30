<?php

namespace Main\Form\Data;

use Main\Form\AbstractFormData;
use Main\Form\DefaultsRuleSets;

class IdFormData extends AbstractFormData
{
    /** @var int */
    protected $id;

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    protected function getRules(): array
    {
        return [
            'id' => DefaultsRuleSets::id(),
        ];
    }
}
