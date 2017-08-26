<?php

namespace Main\Form\Validator;

class NotBlank extends BaseFormValidator
{
    public function execute()
    {
        if (is_null($this->value) || $this->value === '') {
            $this->bindError();
        }
        return $this->getValue();
    }

    protected function getDefaultErrorText(): string
    {
        return 'Значение не должно быть пустым';
    }
}
