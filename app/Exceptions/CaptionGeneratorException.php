<?php

namespace App\Exceptions;

use Exception;

class CaptionGeneratorException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'Failed To Generate The Caption!'
        );
    }
}
