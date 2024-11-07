<?php

namespace App\System\Util\Validator;

class NotEmptyValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (empty($value)) {
            $validator->addError($field, 'Field cannot be empty');
        }
    }
}
