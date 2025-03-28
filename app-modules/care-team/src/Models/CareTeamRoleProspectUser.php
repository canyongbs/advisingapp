<?php

namespace AdvisingApp\CareTeam\Models;

use AdvisingApp\Prospect\Models\Prospect;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CareTeamRoleProspectUser extends Pivot
{
    use HasUuids;
    use HasFactory;

    public function careTeamRole(): BelongsTo
    {
        return $this->belongsTo(CareTeamRole::class);
    }

    public function prospect(): BelongsTo
    {
        return $this->belongsTo(Prospect::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
