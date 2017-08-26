<?php

namespace Main\Form\Data;

use Main\Form\Converter\ToId;
use Main\Form\AbstractFormData;
use Main\Form\Converter\Trim;
use Main\Form\RuleContainer;
use Main\Form\Validator\NotBlank;

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
            'id' => new RuleContainer([ new Trim(), new ToId(), new NotBlank()], 'Неверный Id'),
        ];
    }
}
