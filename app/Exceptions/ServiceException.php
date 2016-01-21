<?php
/**
 * User: dryyun
 * Time: 15/11/3 下午10:37
 * File: ServiceException.php
 */
namespace App\Exceptions;

use Exception;

class ServiceException extends Exception
{

    public function __construct($message, $code = 600)
    {
        parent::__construct($message, $code);
    }
}
