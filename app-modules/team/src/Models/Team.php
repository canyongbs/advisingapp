<?php

namespace Assist\Team\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
