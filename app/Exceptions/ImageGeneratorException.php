<?php

namespace App\Exceptions;

use Exception;

class ImageGeneratorException extends Exception
{
    public function __construct()
    {
        parent::__construct('Failed To Generate The Image');
    }
}
