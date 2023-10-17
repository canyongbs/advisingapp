<?php

namespace Assist\MeetingCenter\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calendar extends BaseModel
{
    protected $hidden = [
        'oauth_token',
        'oauth_refresh_token',
    ];

    protected $casts = [
        'provider_id' => 'encrypted',
        'oauth_token' => 'encrypted',
        'oauth_refresh_token' => 'encrypted',
        'oauth_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }
}
