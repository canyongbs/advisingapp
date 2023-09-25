<?php

namespace Assist\Webhook\Filament\Resources;

use Filament\Resources\Resource;
use Assist\Webhook\Models\InboundWebhook;
use App\Filament\Pages\Concerns\HasNavigationGroup;
use Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages\ViewInboundWebhook;
use Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages\ListInboundWebhooks;

class InboundWebhookResource extends Resource
{
    use HasNavigationGroup;

    protected static ?string $model = InboundWebhook::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    public static function getPages(): array
    {
        return [
            'index' => ListInboundWebhooks::route('/'),
            'view' => ViewInboundWebhook::route('/{record}'),
        ];
    }
}
