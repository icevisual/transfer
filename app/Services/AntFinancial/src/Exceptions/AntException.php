<?php
namespace AntFinancial\Exceptions;

class AntException extends \Exception
{

    public $data = [];

    public function getData()
    {
        return $this->data;
    }

    public function __construct($message, $data = [], $code = 999 )
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }
}
