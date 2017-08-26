<?php

namespace Main\Form;

use Main\Form\Validator\BaseFormValidator;
use Main\Form\Validator\FormValidatorTrait;
use Main\Form\Validator\ValidatorInterface;

class RuleContainer extends BaseFormValidator implements ValidatorInterface
{
    use FormValidatorTrait;

    /** @var AbstractDataValueManager[] */
    protected $rules;
    protected $key = '';

    public function __construct(array $rules = [], $customError = '', $value = null, $key = '')
    {
        parent::__construct($customError, $value);
        $this->setKey($key);
        $this->setRules($rules);
    }

    /**
     * @return AbstractDataValueManager[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    public function setRules(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
        return $this;
    }

    public function execute()
    {
        $filteredValue = $this->getValue();
        foreach ($this->getRules() as $rule) {
            $rule->setValue($filteredValue);
            if ($rule instanceof ValidatorInterface) {
                $rule->setCustomError($this->getCustomError());
            }

            $filteredValue = $rule->execute();
            if ($rule instanceof ValidatorInterface && !$rule->isValid()) {
                $this->setError($rule->getError());
                break;
            }
        }
        return $filteredValue;
    }

    public function getDefaultErrorText(): string
    {
        return '';
    }
}
