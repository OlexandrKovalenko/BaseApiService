<?php

namespace App\System\Util\Validator;

class EmailValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $validator->addError($field, 'Invalid email format');
        }
    }
}
