<?php

namespace App\System\Exception;

use Exception;

class ValidationException extends Exception
{
    protected $message = 'Invalid parameters';
    protected $code = 400;

    public function __construct($message = null, $code = null, Exception $previous = null)
    {
        if ($message) {
            $this->message = $message;
        }

        if ($code) {
            $this->code = $code;
        }

        parent::__construct($this->message, $this->code, $previous);
    }
}