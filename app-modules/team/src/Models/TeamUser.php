<?php

namespace Assist\Team\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperTeamUser
 */
class TeamUser extends Pivot
{
    use HasUuids;

    public function team(): BelongsTo
    {
        return $this->BelongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
