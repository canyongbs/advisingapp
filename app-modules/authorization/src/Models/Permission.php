<?php

namespace Assist\Authorization\Models;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
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

    public function scopeApi(Builder $query): void
    {
        $query->where('guard_name', 'api');
    }

    public function scopeWeb(Builder $query): void
    {
        $query->where('guard_name', 'web');
    }
}
