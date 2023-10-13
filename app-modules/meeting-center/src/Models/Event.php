<?php

namespace Assist\MeetingCenter\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'provider_id',
        'provider_type',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
