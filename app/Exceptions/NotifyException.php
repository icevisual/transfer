<?php

namespace App\Exceptions;

use Exception;

class NotifyException extends Exception
{

    public function __construct($message, $code = 600)
    {
        parent::__construct($message, $code);
    }
}
