<?php

namespace App\Exceptions;

use Exception;

class SoftDeleteContraintViolationException extends Exception
{
    protected $message = 'Soft delete violates foreign key constraint.';
}
