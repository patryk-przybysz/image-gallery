<?php

namespace App\Exceptions;

class ViewNotFoundException extends \Exception
{
    protected $code = 404;
    protected $message = 'View not found';
}
