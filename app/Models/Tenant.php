<?php

namespace App\Models;

use App\Casts\Encrypted;
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
        'key' => Encrypted::class,
        'db_host' => Encrypted::class,
        'db_port' => Encrypted::class,
        'database' => Encrypted::class,
        'db_username' => Encrypted::class,
        'db_password' => Encrypted::class,
        'sis_db_host' => Encrypted::class,
        'sis_db_port' => Encrypted::class,
        'sis_database' => Encrypted::class,
        'sis_db_username' => Encrypted::class,
        'sis_db_password' => Encrypted::class,
    ];
}
