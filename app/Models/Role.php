<?php

namespace App\Models;

use Illuminate\Support\Collection;
use App\Models\Concerns\HasRoleGroups;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;
use App\Models\Concerns\DefinesPermissions;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends SpatieRole
{
    use DefinesPermissions;
    use HasRoleGroups;

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_roles'),
            PermissionRegistrar::$pivotRole,
            config('permission.column_names.model_morph_key')
        )->withPivot('via');
    }

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
