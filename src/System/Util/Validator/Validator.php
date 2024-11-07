<?php

namespace App\System\Util\Validator;

class Validator
{
    private array $errors = [];

    public function validate(array $data, array $rules): bool
    {
        foreach ($rules as $field => $ruleset) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $ruleset) as $rule) {
                $ruleName = $rule;
                $ruleParam = null;

                // Check for parameters in the rule
                if (str_contains($rule, ':')) {
                    [$ruleName, $ruleParam] = explode(':', $rule);
                }

                // Execute the validation rule
                $this->executeValidationRule($field, $value, $ruleName, $ruleParam);
            }
        }

        return empty($this->errors);
    }

    private function executeValidationRule(string $field, $value, string $ruleName, ?string $ruleParam): void
    {

        $validator = $this->getValidator($ruleName);
        $validator?->validate($field, $value, $ruleParam, $this);
    }

    private function getValidator(string $ruleName): ?ValidatorStrategy
    {
        $validators = [
            'notEmpty' => new NotEmptyValidator(),
            'email' => new EmailValidator(),
            'min' => new MinLengthValidator(),
            'max' => new MaxLengthValidator(),
            'string' => new StringValidator(),
            'array' => new ArrayValidator(),
            'int' => new IntValidator(),
            'float' => new FloatValidator(),
            'datetime' => new DateTimeValidator(),
            'instanceOf' => new InstanceOfValidator(),
            'phone' => new PhoneValidator(),
        ];

        return $validators[$ruleName] ?? null;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
