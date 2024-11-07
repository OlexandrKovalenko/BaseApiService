<?php

namespace App\System\Exception;

use Exception;

class UserNotFoundException extends Exception
{
    protected $message = 'User not found';
    protected $code = 404;

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