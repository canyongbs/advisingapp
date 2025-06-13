<?php

namespace AdvisingApp\Engagement\Models;

use AdvisingApp\Engagement\Enums\EngagementResponseType;
use App\Models\BaseModel;

/**
 * @mixin IdeHelperUnmatchedInboundCommunication
 */
class UnmatchedInboundCommunication extends BaseModel
{
    protected $fillable = [
        'sender',
        'occurred_at',
        'subject',
        'type',
        'body',
    ];

    protected $casts = [
        'occurred_at' => 'timestamp',
        'type' => EngagementResponseType::class,
    ];
}
