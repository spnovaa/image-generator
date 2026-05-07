<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class ImageGeneratorException extends RuntimeException
{
    public function __construct(string $message = 'Failed to generate the image.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
