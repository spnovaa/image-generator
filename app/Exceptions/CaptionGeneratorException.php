<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class CaptionGeneratorException extends RuntimeException
{
    public function __construct(string $message = 'Failed to generate the caption.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
