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
            'login' => new RuleContainer([new Trim(), new NotBlank()]),
            'password' => new RuleContainer([new Trim(), new NotBlank()]),
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
