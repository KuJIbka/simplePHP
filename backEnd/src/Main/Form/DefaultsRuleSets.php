<?php

namespace Main\Form;

use Main\Form\Converter\ToId;
use Main\Form\Converter\Trim;
use Main\Form\Validator\Min;

class DefaultsRuleSets
{
    public static function id(): RuleContainer
    {
        return new RuleContainer([new Trim(), new ToId(), new Min(1)], 'L_ERROR_WRONG_ID');
    }
}
