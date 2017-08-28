<?php

namespace Main\Form\Validator;

use Main\Form\DataManager;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

interface ValidatorInterface extends DataManager
{
    public function isValid(): bool;

    /**
     * @return  string|LocalisationString|LocalisationChoiceString $error
     */
    public function getError();

    /**
     * @param string|LocalisationString|LocalisationChoiceString $error
    */
    public function setCustomError($error);
}
