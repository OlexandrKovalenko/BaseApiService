<?php
declare(strict_types=1);

/**
 * @file UserException.php
 * @author maslo
 * @since 12.11.2024
 */

namespace App\System\Exception;

use Exception;

/**
 * Class UserException
 *
 * Custom exception class for handling user-related errors within the system.
 * This class provides predefined constants and methods for common user error types,
 * such as user not found, validation errors, and internal server issues.
 * It simplifies error handling by allowing the creation of specific exceptions
 * for common scenarios with relevant HTTP status codes and error messages.
 *
 * Usage example:
 * throw UserException::userNotFound(); // Throws a 404 User Not Found exception.
 *
 * @package App\System\Exception
 * @since 12.11.2024
 */
class UserException extends Exception
{
    /**
     *
     */
    const int USER_NOT_FOUND = 404;
    /**
     *
     */
    const int VALIDATION_ERROR = 400;
    /**
     *
     */
    const int INTERNAL_ERROR = 500;

    /**
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(string $message, int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * userNotFound
     *
     * @return self
     */
    public static function userNotFound(): self
    {
        return new self('User not found', self::USER_NOT_FOUND);
    }

    /**
     * validationError
     *
     * @param string $message
     * @return self
     */
    public static function validationError(string $message): self
    {
        return new self($message, self::VALIDATION_ERROR);
    }

    /**
     * internalError
     *
     * @param string $message
     * @return self
     */
    public static function internalError(string $message): self
    {
        return new self($message, self::INTERNAL_ERROR);
    }
}