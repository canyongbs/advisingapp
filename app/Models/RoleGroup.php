<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Pivots\RoleGroupPivot;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\RoleGroups\HasDefaults;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class RoleGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasRelationships;
    use HasSlug;
    use HasDefaults;

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
