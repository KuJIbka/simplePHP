<?php

namespace Main\Form;

use Main\Form\Converter\BaseConverter;
use Main\Form\Validator\BaseFormValidator;
use Main\Form\Validator\FormValidatorTrait;
use Main\Form\Validator\ValidatorInterface;

class RuleContainer extends AbstractDataValueManager implements ValidatorInterface
{
    use FormValidatorTrait;

    /** @var AbstractDataValueManager[] */
    protected $rules = [];
    protected $key = '';

    public function __construct(array $rules = [], $customError = '')
    {
        $this->setCustomError($customError);
        $this->setRules($rules);
    }

    /**
     * @return AbstractDataValueManager[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param AbstractDataValueManager[] $rules
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->rules = $rules;
        return $this;
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
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
        return $this;
    }

    public function execute()
    {
        $filteredValue = $this->getValue();
        if (is_array($filteredValue)) {
            foreach ($filteredValue as $k => $v) {
                echo "\n";
                $wasError = false;
                foreach ($this->getRules() as $rule) {
                    $rule->setValue($v);
                    if ($rule instanceof BaseConverter) {
                        $v = $rule->convert();
                    } elseif ($rule instanceof BaseFormValidator) {
                        $rule->setCustomError($this->getCustomError());
                        $rule->check();
                        if (!$rule->isValid()) {
                            $this->setError($rule->getError());
                            $wasError = true;
                            break;
                        }
                    }
                }
                if ($wasError) {
                    break;
                } else {
                    $filteredValue[$k] = $v;
                }
            }
        } else {
            foreach ($this->getRules() as $rule) {
                $rule->setValue($filteredValue);
                if ($rule instanceof BaseConverter) {
                    $filteredValue = $rule->convert();
                }
                if ($rule instanceof BaseFormValidator) {
                    $rule->setCustomError($this->getCustomError());
                    $rule->check();
                    if (!$rule->isValid()) {
                        $this->setError($rule->getError());
                        break;
                    }
                }
            }
        }
        return $filteredValue;
    }
}
