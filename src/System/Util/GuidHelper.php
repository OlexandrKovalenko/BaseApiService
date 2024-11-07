<?php

namespace App\System\Util;

use Random\RandomException;

class GuidHelper
{
    private static ?string $globalSessionId = null;

    /**
     * Генерація глобального ID для сесії, яка триває протягом всього запиту.
     * Якщо сесія вже створена, повертає існуючий ідентифікатор.
     *
     * @throws RandomException
     */
    public static function getOrCreateGlobalSessionId(): string
    {
        if (self::$globalSessionId === null) {
            self::$globalSessionId = self::generateGuid();
        }
        return self::$globalSessionId;
    }

    /**
     * Генерація локального ID для короткочасної сесії, яка створюється для конкретного процесу/функції.
     *
     * @throws RandomException
     */
    public static function createLocalSessionId(): string
    {
        return self::generateGuid();
    }

    /**
     * Генерація GUID за стандартом RFC 4122
     *
     * @throws RandomException
     */
    private static function generateGuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // Версія 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // Непередбачуваний варіант

        return vsprintf('%.8s-%.4s-%.4s-%.4s-%.12s', str_split(bin2hex($data), 4));
    }

    /**
     * Скидання глобального ID сесії (якщо потрібно).
     */
    public static function resetGlobalSessionId(): void
    {
        self::$globalSessionId = null;
    }
}
