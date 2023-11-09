<?php

namespace Assist\Team\Models;

use App\Models\User;
use App\Models\BaseModel;
use Assist\Division\Models\Division;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @mixin IdeHelperTeam
 */
class Team extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(TeamUser::class)
            ->withTimestamps();
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
}
