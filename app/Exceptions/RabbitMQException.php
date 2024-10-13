<?php

namespace App\Exceptions;

use Exception;

class RabbitMQException extends Exception
{
    public function __construct()
    {
        parent::__construct('Message Broker Error!');
    }
}
