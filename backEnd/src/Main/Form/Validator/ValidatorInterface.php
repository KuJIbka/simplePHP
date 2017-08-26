<?php

namespace Main\Form\Validator;

use Main\Form\DataManager;

interface ValidatorInterface extends DataManager
{
    public function isValid();
    public function getError();
    public function setCustomError($error);
}
