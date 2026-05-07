<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class GeneralDatabaseException extends RuntimeException
{
    public function __construct(string $message = 'Database error.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
