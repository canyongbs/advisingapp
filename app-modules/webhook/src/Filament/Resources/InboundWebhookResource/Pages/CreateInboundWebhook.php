<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Assist\Webhook\Filament\Resources\InboundWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInboundWebhook extends CreateRecord
{
    protected static string $resource = InboundWebhookResource::class;
}
