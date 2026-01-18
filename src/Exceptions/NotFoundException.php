<?php
/**
 * Not Found Exception
 */

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    public function __construct(string $message = "Resource not found")
    {
        parent::__construct($message, 404);
    }
}

