<?php

namespace App\System\Util\Validator;

use App\System\Util\Validator\ValidatorStrategy;

class PhoneValidator implements ValidatorStrategy
{

    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        if (!preg_match('/^380\d{9}$/', $value)) {
            $validator->addError($field, 'Номер телефону має починатися з 380 та містити 12 цифр.');
        }
    }
}