<?php

namespace App\Exceptions;

class RouteNotFoundException extends \Exception
{
    protected $code  = 404;
    protected $message = 'Not Found';
}
