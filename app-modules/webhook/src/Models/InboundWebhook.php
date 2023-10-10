<?php

namespace Assist\Webhook\Models;

use App\Models\BaseModel;
use Assist\Webhook\Enums\InboundWebhookSource;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @mixin IdeHelperInboundWebhook
 */
class InboundWebhook extends BaseModel
{
    use DefinesPermissions;

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
