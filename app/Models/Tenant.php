<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as SpatieTenant;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

class Tenant extends SpatieTenant
{
    use UsesLandlordConnection;

    protected $fillable = [
        'name',
        'domain',
        'db_host',
        'db_port',
        'database',
        'db_username',
        'db_password',
        'sis_db_host',
        'sis_db_port',
        'sis_database',
        'sis_db_username',
        'sis_db_password',
    ];
}
