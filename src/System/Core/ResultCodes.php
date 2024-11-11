<?php

namespace App\System\Core;

/**
 * Class ResultCodes
 *
 * @package App\System\Core
 * @author maslo
 * @since 08.11.2024
 */
class ResultCodes
{
// Коди успіху
    const int SUCCESS = 10;

    // Коди помилок
    const int ERROR_NOT_FOUND = 404;
    const int ERROR_BAD_REQUEST = 400;
    const int ERROR_INTERNAL_SERVER = 500;
    const int ERROR_UNAUTHORIZED = 401;
    const int ERROR_FORBIDDEN = 403;
}