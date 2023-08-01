<?php

namespace Assist\Authorization\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Assist\Authorization\Models\Pivots\RoleGroupPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Assist\Authorization\Models\RoleGroup
 *
 * @property int $id
 * @property string $name
 * @property string|null $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Authorization\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 *
 * @method static \Assist\Authorization\Database\Factories\RoleGroupFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroup withoutTrashed()
 *
 * @mixin \Eloquent
 */
class RoleGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasRelationships;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function users(): MorphToMany
    {
        return $this
            ->morphedByMany(User::class, 'role_groupable')
            ->using(RoleGroupPivot::class);
    }

    public function roles(): MorphToMany
    {
        return $this
            ->morphedByMany(Role::class, 'role_groupable')
            ->using(RoleGroupPivot::class);
    }

    public function permissions(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    }
}
