<?php

namespace Main\Form\Data;

use Form\DefaultsRuleSets;
use Main\Form\AbstractFormData;
use Main\Form\RuleContainer;

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

    /**
     * @return RuleContainer[]
     */
    protected function getRules(): array
    {
        return [
            'id' => DefaultsRuleSets::id(),
        ];
    }
}
