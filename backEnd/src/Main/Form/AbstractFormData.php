<?php

namespace Main\Form;

use Main\Exception\BaseFormDataException;
use Main\Form\Validator\FormValidatorTrait;
use Main\Form\Validator\ValidatorInterface;
use Main\Service\TranslationsService;
use Main\Struct\LocalisationChoiceString;
use Main\Struct\LocalisationString;

abstract class AbstractFormData implements ValidatorInterface
{
    use FormValidatorTrait;

    /**
     * @var string
     */
    protected $csrfToken;

    /**
     * @var array
     */
    protected $sourceData;

    /**
     * @param string[]|LocalisationString[]|LocalisationChoiceString[] $error
     */
    private $errors = [];

    /**
     * @return RuleContainer[]
     */
    abstract protected function getRules(): array;

    /**
     * @param array $data
     * @param bool $checkCsrfToken
     * @throws \Exception
     */
    public function __construct(array $data, bool $checkCsrfToken = false)
    {
        $this->sourceData = $data;
        $this->execute();
    }

    /**
     * @throws \Exception
     */
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
            }
            $this->$param = $filteredValue;
        }
    }

    /**
     * @return string[]|LocalisationString[]|LocalisationChoiceString[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @throws BaseFormDataException
     */
    public function isValidWithThrowException()
    {
        if (!$this->isValid()) {
            throw (new BaseFormDataException())->setFormDataErrors($this->getTranslatedErrorsData());
        }
    }

    /**
     * @return string[]|LocalisationString[]|LocalisationChoiceString[]
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
     * @return string[]
     */
    public function getTranslatedErrorsData(): array
    {
        $result = [];
        $translator = TranslationsService::get()->getTranslator();
        foreach ($this->getFormsErrorsData() as $param => $error) {
            if ($error instanceof LocalisationChoiceString) {
                $result[$param] = $translator->transChoice($error);
            } else {
                $result[$param] = $translator->trans($error);
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getTranslatedErrorsOneString(): string
    {
        $result = '';
        $first = true;
        foreach ($this->getFormsErrorsData() as $param => $error) {
            if ($first) {
                $first = false;
            } else {
                $result .= '\n';
            }
            $result .= TranslationsService::get()->getTranslator()->trans($error);
        }
        return $result;
    }
}
