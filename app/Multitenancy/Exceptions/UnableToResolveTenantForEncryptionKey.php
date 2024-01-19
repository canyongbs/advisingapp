<?php

namespace App\Multitenancy\Exceptions;

use Exception;

class UnableToResolveTenantForEncryptionKey extends Exception
{
    protected $message = 'Unable to resolve tenant for encryption key';
}
