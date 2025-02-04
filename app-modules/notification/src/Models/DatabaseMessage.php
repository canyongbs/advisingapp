<?php

namespace AdvisingApp\Notification\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DatabaseMessage extends BaseModel
{
    protected $fillable = [
        'notification_class',
        'notification_id',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo(
            name: 'related',
            type: 'related_type',
            id: 'related_id',
        );
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }
}
