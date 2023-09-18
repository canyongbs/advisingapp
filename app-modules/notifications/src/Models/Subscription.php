<?php

namespace Assist\Notifications\Models;

use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSubscription
 */
class Subscription extends BaseModel
{
    use SoftDeletes;

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
