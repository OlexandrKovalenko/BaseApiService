<?php

namespace App\System\Util\Validator;

class InstanceOfValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if ($ruleParam && !($value instanceof $ruleParam)) {
            $validator->addError($field, "Value must be an instance of $ruleParam");
        }
    }
}
