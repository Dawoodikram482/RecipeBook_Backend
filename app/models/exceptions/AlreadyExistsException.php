<?php

namespace Models\Exceptions;

use Exception;

class AlreadyExistsException extends InternalErrorException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}