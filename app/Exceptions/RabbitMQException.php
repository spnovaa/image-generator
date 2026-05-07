<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class RabbitMQException extends RuntimeException
{
    public function __construct(string $message = 'Message broker error.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
