<?php

namespace App\System\Util\Validator;

class MaxLengthValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (strlen($value) > (int)$ruleParam) {
            $validator->addError($field, "Maximum length is $ruleParam characters");
        }
    }
}
