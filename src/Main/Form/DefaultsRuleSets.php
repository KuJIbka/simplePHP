<?php

namespace Form;

use Main\Form\Converter\ToId;
use Main\Form\Converter\Trim;
use Main\Form\RuleContainer;

class DefaultsRuleSets
{
    public static function id(): RuleContainer
    {
        return new RuleContainer([new Trim(), new ToId()], 'Неверный ID');
    }
}
