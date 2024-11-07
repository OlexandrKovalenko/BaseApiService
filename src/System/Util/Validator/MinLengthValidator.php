<?php

namespace App\System\Util\Validator;

class MinLengthValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (strlen($value) < (int)$ruleParam) {
            $validator->addError($field, "Minimum length is $ruleParam characters");
        }
    }
}
