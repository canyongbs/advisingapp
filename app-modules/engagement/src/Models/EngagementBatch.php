<?php

namespace Assist\Engagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Engagement\Models\Concerns\HasManyEngagements;

/**
 * @mixin IdeHelperEngagementBatch
 */
class EngagementBatch extends BaseModel
{
    use HasManyEngagements;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
