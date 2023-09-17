<?php

namespace Assist\Engagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\Engagement\Models\Concerns\HasManyEngagements;

/**
 * Assist\Engagement\Models\EngagementBatch
 *
 * @property string $id
 * @property string|null $identifier
 * @property string $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Assist\Engagement\Models\Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read User $user
 *
 * @method static \Assist\Engagement\Database\Factories\EngagementBatchFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementBatch whereUserId($value)
 *
 * @mixin \Eloquent
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
