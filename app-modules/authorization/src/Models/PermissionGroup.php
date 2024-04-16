<?php

namespace AdvisingApp\Authorization\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperPermissionGroup
 */
class PermissionGroup extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'group_id');
    }
}
