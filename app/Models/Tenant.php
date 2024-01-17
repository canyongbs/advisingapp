<?php

namespace App\Models;

use App\Casts\TenantEncrypted;
use App\Casts\LandlordEncrypted;
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
        'key' => LandlordEncrypted::class,
        'config' => TenantEncrypted::class,
    ];
}
