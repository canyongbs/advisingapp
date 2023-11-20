<?php

namespace Assist\Webhook\Models;

use App\Models\BaseModel;
use Illuminate\Support\Collection;
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

    public function getWebPermissions(): Collection
    {
        return collect(['view-any', '*.view']);
    }

    public function getApiPermissions(): Collection
    {
        return collect([]);
    }
}
