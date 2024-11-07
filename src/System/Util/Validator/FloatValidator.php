<?php

namespace App\System\Util\Validator;

class FloatValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (!is_float($value) && !is_numeric($value)) {
            $validator->addError($field, 'Value must be a float');
        }
    }
}
