<?php

namespace App\System\Util\Validator;

class ArrayValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (!is_array($value)) {
            $validator->addError($field, 'Value must be an array');
        }
    }
}
