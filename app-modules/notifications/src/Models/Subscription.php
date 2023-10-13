<?php

namespace Assist\Notifications\Models;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends MorphPivot
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    public $timestamps = true;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'subscribable_id',
        'subscribable_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
