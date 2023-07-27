<?php

namespace App\Models\Concerns\RoleGroups;

use Illuminate\Contracts\Database\Eloquent\Builder;

trait HasDefaults
{
    public static function scopeSuperAdmin(Builder $query): void
    {
        $query->where('slug', 'super_admin');
    }

    public static function scopeAdmin(Builder $query): void
    {
        $query->where('slug', 'admin');
    }
}
