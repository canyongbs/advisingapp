<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EngagementResponse extends BaseModel
{
    protected $fillable = [
        'sender_id',
        'sender_type',
        'content',
    ];

    public function sender(): MorphTo
    {
        return $this->morphTo(
            name: 'sender',
            type: 'sender_type',
            id: 'sender_id',
        );
    }
}
