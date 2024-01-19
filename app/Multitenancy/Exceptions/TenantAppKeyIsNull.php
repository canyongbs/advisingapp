<?php

namespace App\Multitenancy\Exceptions;

use Exception;

class TenantAppKeyIsNull extends Exception
{
    protected $message = 'Tenant app key is null';
}
