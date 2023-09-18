<?php

namespace Assist\Webhook\Models;

use App\Models\BaseModel;
use Assist\Webhook\Enums\InboundWebhookSource;

/**
 * @mixin IdeHelperInboundWebhook
 */
class InboundWebhook extends BaseModel
{
    protected $fillable = [
        'source',
        'event',
        'url',
        'payload',
    ];

    protected $casts = [
        'source' => InboundWebhookSource::class,
    ];
}
