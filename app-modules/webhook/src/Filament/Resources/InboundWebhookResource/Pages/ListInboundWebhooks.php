<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;

class ListInboundWebhooks extends ListRecords
{
    protected static string $resource = InboundWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
