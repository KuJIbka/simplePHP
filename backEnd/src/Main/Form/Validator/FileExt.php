<?php

namespace Main\Form\Validator;

use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

class FileExt extends BaseFormValidator
{
    protected $availableExts;

    public function __construct(array $availableExts, bool $canBeEmpty = false, $customError = '')
    {
        parent::__construct($canBeEmpty, $customError);
        $this->availableExts = $availableExts;
    }

    /**
     * @return string|LocalisationString|LocalisationChoiceString
     */
    protected function getDefaultErrorText()
    {
        return new LocalisationString(
            'L_ERROR_WRONG_FILE_EXT',
            ['%availableExts%' => implode(', ', $this->availableExts)]
        );
    }

    protected function doCheck()
    {
        $value = $this->getValue();
        $ext = strtoupper(pathinfo($value['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $this->availableExts)) {
            $this->bindError();
        }
    }
}
