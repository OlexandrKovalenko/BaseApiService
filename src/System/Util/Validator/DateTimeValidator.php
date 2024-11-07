<?php

namespace App\System\Util\Validator;

class DateTimeValidator implements ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void
    {
        $dateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if (!$dateTime || $dateTime->format('Y-m-d H:i:s') !== $value) {
            $validator->addError($field, 'Value must be a valid datetime format (Y-m-d H:i:s)');
        }
    }
}
