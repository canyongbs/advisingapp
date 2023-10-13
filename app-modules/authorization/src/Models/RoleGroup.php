<?php

namespace Assist\Authorization\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;
use Assist\Authorization\Models\Pivots\RoleGroupUserPivot;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperRoleGroup
 */
class RoleGroup extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use HasRelationships;
    use HasUuids;
    use AuditableTrait;
    use DefinesPermissions;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(RoleGroupUserPivot::class);
    }

    public function roles(): BelongsToMany
    {
        return $this
            ->belongsToMany(Role::class)
            ->using(RoleGroupRolePivot::class);
    }

    public function permissions(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    }
}
