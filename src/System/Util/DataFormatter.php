<?php

namespace App\System\Util;

class DataFormatter
{
    public static function formatPhone(string $phone): string
    {
        $phone = trim($phone);
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 10 && $phone[0] === '0') {
            $phone = '38' . $phone;
        }

        if (strlen($phone) === 12) {
            return $phone;
        }

        return '';
    }
}