<?php

namespace Assist\Authorization\Models;

use Eloquent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\HasRoleGroups;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Authorization\Models\Role
 *
 * @property string $id
 * @property string $name
 * @property string $guard_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $roleGroups
 * @property-read int|null $role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\RoleGroup> $traitRoleGroups
 * @property-read int|null $trait_role_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static Builder|Role api()
 * @method static \Assist\Authorization\Database\Factories\RoleFactory factory($count = null, $state = [])
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static Builder|Role query()
 * @method static Builder|Role superAdmin()
 * @method static Builder|Role web()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereGuardName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class Role extends SpatieRole implements Auditable
{
    use HasFactory;
    use DefinesPermissions;
    use HasRoleGroups {
        HasRoleGroups::roleGroups as traitRoleGroups;
    }
    use HasUuids;
    use AuditableTrait;

    public function roleGroups(): BelongsToMany
    {
        return $this->traitRoleGroups()
            ->using(RoleGroupRolePivot::class);
    }

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
        $query->where('name', 'authorization.super_admin');
    }
}
