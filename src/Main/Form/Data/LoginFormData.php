<?php

namespace Main\Form\Data;

use Main\Form\AbstractFormData;
use Main\Form\Converter\Trim;
use Main\Form\RuleContainer;
use Main\Form\Validator\NotBlank;

class LoginFormData extends AbstractFormData
{
    protected $login;
    protected $password;

    /**
     * @return RuleContainer[]
     */
    protected function getRules(): array
    {
        return [
            'login' => new RuleContainer([new Trim(), new NotBlank('Логин не должен быть пустым')]),
            'password' => new RuleContainer([new Trim(), new NotBlank('Пароль не должен быть пустым')]),
        ];
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
