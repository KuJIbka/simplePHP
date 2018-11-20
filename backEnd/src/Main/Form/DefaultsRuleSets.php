<?php

namespace Main\Form;

use Main\Form\Converter\ToId;
use Main\Form\Converter\Trim;
use Main\Form\Validator\Min;
use Main\Form\Validator\UserDefinedValidator;

class DefaultsRuleSets
{
    public static function id(): RuleContainer
    {
        return new RuleContainer([new Trim(), new ToId(), new Min(1)], 'L_ERROR_WRONG_ID');
    }

    public static function file(): RuleContainer
    {
        return new RuleContainer([new UserDefinedValidator(function ($value, $validator) {
            /** @var UserDefinedValidator $validator */
            if (isset($value['error']) && $value['error'] !== UPLOAD_ERR_OK) {
                if ($value['error'] === UPLOAD_ERR_NO_FILE) {
                    return null;
                }
                $validator->bindError();
            }
            return $value;
        })], '', true);
    }
}
