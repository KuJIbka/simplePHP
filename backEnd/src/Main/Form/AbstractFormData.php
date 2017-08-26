<?php

namespace Main\Form;

use Main\Form\Validator\FormValidatorTrait;
use Main\Form\Validator\ValidatorInterface;

abstract class AbstractFormData implements DataManager, ValidatorInterface
{
    use FormValidatorTrait;

    protected $csrfToken;
    protected $sourceData;
    private $errors = [];

    /**
     * @return RuleContainer[]
     */
    abstract protected function getRules(): array;

    public function __construct($data, $checkCsrfToken = false)
    {
        $this->sourceData = $data;
        $this->execute();
    }

    public function execute()
    {
        foreach ($this->getRules() as $param => $ruleContainer) {
            if (!property_exists($this, $param)) {
                throw new \Exception('Class "' . self::class . '" does not contain "' . $param . '" attribute');
            }
            $value = isset($this->sourceData[$param]) ? $this->sourceData[$param] : null;
            $ruleContainer->setValue($value);
            if ($this->getCustomError() !== '') {
                $ruleContainer->setCustomError($this->getCustomError());
            }
            $filteredValue = $ruleContainer->execute();
            if (!$ruleContainer->isValid()) {
                $this->errors[$param] = $ruleContainer->getError();
                break;
            }
            $this->$param = $filteredValue;
        }
        return true;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getError()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * выдает список ошибок в виде key-value массива, где key - имя атрибута, value - соответствующая атрибуту ошибка
     * @return array
     */
    public function getFormsErrorsData(): array
    {
        if ($this->isValid()) {
            return [];
        }

        $result = [];
        foreach ($this->errors as $param => $error) {
            $result[$param] = $error;
        }
        return $result;
    }

    /**
     * список ошибок валидации одной строкой
     * @return string
     */
    public function getTranslatedErrors(): string
    {
        if ($this->isValid()) {
            return '';
        }

        $translatedErrors = $this->getFormsErrorsData();
        return implode(",\n ", $translatedErrors);
    }
}
