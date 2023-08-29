<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;

class EditInboundWebhook extends EditRecord
{
    protected static string $resource = InboundWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
