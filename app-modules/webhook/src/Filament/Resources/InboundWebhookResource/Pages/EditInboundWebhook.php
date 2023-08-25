<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Assist\Webhook\Filament\Resources\InboundWebhookResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
