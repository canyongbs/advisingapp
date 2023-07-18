<?php

namespace App\Models;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Concerns\DefinesPermissions;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
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

    public function scopeApi(Builder $query): void
    {
        $query->where('guard_name', 'api');
    }

    public function scopeWeb(Builder $query): void
    {
        $query->where('guard_name', 'web');
    }

    public function scopeSuperAdmin(Builder $query): void
    {
        $query->where('name', 'super_admin');
    }

    public function scopeAdmin(Builder $query): void
    {
        $query->where('name', 'admin');
    }
}
