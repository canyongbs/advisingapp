<?php

namespace Assist\Notifications\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends BaseModel
{
    protected $fillable = [
        'user_id',
        'subscribable_id',
        'subscribable_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscribable()
    {
        return $this->morphTo();
    }
}
