<?php

namespace App\Models;

use App\Multitenancy\DataTransferObjects\TenantConfig;
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
        'config',
    ];

    protected $casts = [
        //'key' => 'encrypted',
        'config' => TenantConfig::class,
    ];
}
