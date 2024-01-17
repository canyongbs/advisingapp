<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Tenant as SpatieTenant;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

/**
 * @mixin IdeHelperTenant
 */
class Tenant extends SpatieTenant
{
    use UsesLandlordConnection;

    protected $fillable = [
        'name',
        'domain',
        'key',
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

    protected $casts = [
        'key' => 'encrypted',
        'db_host' => 'encrypted',
        'db_port' => 'encrypted',
        'database' => 'encrypted',
        'db_username' => 'encrypted',
        'db_password' => 'encrypted',
        'sis_db_host' => 'encrypted',
        'sis_db_port' => 'encrypted',
        'sis_database' => 'encrypted',
        'sis_db_username' => 'encrypted',
        'sis_db_password' => 'encrypted',
    ];
}
