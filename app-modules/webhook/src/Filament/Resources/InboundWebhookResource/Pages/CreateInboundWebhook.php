<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;

class CreateInboundWebhook extends CreateRecord
{
    protected static string $resource = InboundWebhookResource::class;
}
