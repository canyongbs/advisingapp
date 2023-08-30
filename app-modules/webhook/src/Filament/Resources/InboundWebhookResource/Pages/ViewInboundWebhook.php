<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;

class ViewInboundWebhook extends ViewRecord
{
    protected static string $resource = InboundWebhookResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('source')
                    ->translateLabel(),
                TextEntry::make('event')
                    ->translateLabel(),
                TextEntry::make('url')
                    ->translateLabel(),
                TextEntry::make('payload')
                    ->translateLabel(),
            ]);
    }
}
