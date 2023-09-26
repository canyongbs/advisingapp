<?php

namespace Assist\Team\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function users(): HasMany
    {
        return $this
            ->hasMany(User::class);
    }
}
