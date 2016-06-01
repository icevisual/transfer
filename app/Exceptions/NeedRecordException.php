<?php

namespace App\Exceptions;

use Exception;

class NeedRecordException extends Exception
{

    public $data = [];
    
    public function getData(){
        return  $this->data;
    }
    
    public function __construct($message,$data = [], $code = 6000)
    {
        parent::__construct($message, $code);
        $this->data = $data ;
    }
}
