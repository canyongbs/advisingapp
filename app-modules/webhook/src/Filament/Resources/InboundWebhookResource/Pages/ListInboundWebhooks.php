<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Assist\Webhook\Filament\Resources\InboundWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
