<?php

namespace App\Models;

use Illuminate\Support\Collection;
use App\Models\Concerns\DefinesPermissions;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use DefinesPermissions;

    public function getWebPermissions(): Collection
    {
        return collect(['view-any', '*.view']);
    }

    public function getApiPermissions(): Collection
    {
        return collect([]);
    }
}
