<?php

namespace Assist\Webhook\Actions;

use Assist\Webhook\Models\InboundWebhook;
use Assist\Webhook\Enums\InboundWebhookSource;

class StoreInboundWebhook
{
    public function handle(InboundWebhookSource $source, string $event, string $url, string $payload): void
    {
        InboundWebhook::create([
            'source' => $source->value,
            'event' => $event,
            'url' => $url,
            'payload' => $payload,
        ]);
    }
}
