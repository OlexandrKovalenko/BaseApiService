<?php

namespace App\System\Util\Validator;

class StringValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (!is_string($value)) {
            $validator->addError($field, 'Value must be a string');
        }
    }
}
