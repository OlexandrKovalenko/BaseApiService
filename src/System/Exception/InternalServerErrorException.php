<?php

namespace App\System\Exception;

use Exception;

class InternalServerErrorException extends Exception
{
    protected $message = 'Internal server error';
    protected $code = 500;

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