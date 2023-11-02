<?php

namespace Assist\CareTeam\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @mixin IdeHelperCareTeam
 */
class CareTeam extends MorphPivot
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    public $timestamps = true;

    protected $table = 'care_teams';

    /** @return MorphTo<Educatable> */
    public function educatable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTimestamps();
    }
}
