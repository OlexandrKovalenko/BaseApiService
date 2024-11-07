<?php

namespace App\System\Util\Validator;

class IntValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (!is_int($value)) {
            $validator->addError($field, 'Value must be an integer');
        }
    }
}
