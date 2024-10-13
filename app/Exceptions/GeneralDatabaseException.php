<?php

namespace App\Exceptions;

use Exception;

class GeneralDatabaseException extends Exception
{
    public function __construct()
    {
        parent::__construct('Database Error!');
    }
}
