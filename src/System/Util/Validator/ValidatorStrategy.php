<?php

namespace App\System\Util\Validator;

interface ValidatorStrategy
{
    public function validate(string $field, $value, ?string $ruleParam, Validator $validator): void;
}
