<?php

namespace AdvisingApp\Notification\Models;

use AdvisingApp\Notification\Enums\EmailMessageEventType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailMessageEvent extends BaseModel
{
    protected $fillable = [
        'type',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'type' => EmailMessageEventType::class,
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class);
    }
}
