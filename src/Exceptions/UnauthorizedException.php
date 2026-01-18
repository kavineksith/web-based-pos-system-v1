<?php
/**
 * Unauthorized Exception
 */

namespace App\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public function __construct(string $message = "Unauthorized access")
    {
        parent::__construct($message, 401);
    }
}

