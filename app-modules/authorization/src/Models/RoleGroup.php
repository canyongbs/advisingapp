<?php

namespace Assist\Authorization\Models;

use Eloquent;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;
use Assist\Authorization\Models\Pivots\RoleGroupUserPivot;

/**
 * Assist\Authorization\Models\RoleGroup
 *
 * @property string $id
 * @property string $name
 * @property string|null $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Assist\Authorization\Database\Factories\RoleGroupFactory factory($count = null, $state = [])
 * @method static Builder|RoleGroup newModelQuery()
 * @method static Builder|RoleGroup newQuery()
 * @method static Builder|RoleGroup onlyTrashed()
 * @method static Builder|RoleGroup query()
 * @method static Builder|RoleGroup whereCreatedAt($value)
 * @method static Builder|RoleGroup whereDeletedAt($value)
 * @method static Builder|RoleGroup whereId($value)
 * @method static Builder|RoleGroup whereName($value)
 * @method static Builder|RoleGroup whereSlug($value)
 * @method static Builder|RoleGroup whereUpdatedAt($value)
 * @method static Builder|RoleGroup withTrashed()
 * @method static Builder|RoleGroup withoutTrashed()
 *
 * @mixin Eloquent
 */
class RoleGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasRelationships;
    use HasUuids;

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
