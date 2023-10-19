<?php

namespace Assist\MeetingCenter\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEvent extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'provider_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function calendar(): BelongsTo
    {
        return $this->belongsTo(Calendar::class);
    }
}
