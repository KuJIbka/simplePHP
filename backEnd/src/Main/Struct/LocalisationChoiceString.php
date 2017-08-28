<?php

namespace Main\Struct;

class LocalisationChoiceString extends LocalisationString
{
    protected $choiceNumber = 0;

    public function __construct($key = '', $choiceNumber = 0, array $data = [], $locale = null, $domain = null)
    {
        parent::__construct($key, $data, $locale, $domain);
        $this->setChoiceNumber($choiceNumber);
    }

    public function getChoiceNumber(): int
    {
        return $this->choiceNumber;
    }

    public function setChoiceNumber(int $choiceNumber = 0)
    {
        $this->choiceNumber = $choiceNumber;
        return $this;
    }
}
