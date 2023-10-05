<?php

namespace Assist\Webhook\Filament\Resources\InboundWebhookResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Webhook\Filament\Resources\InboundWebhookResource;

class ViewInboundWebhook extends ViewRecord
{
    protected static string $resource = InboundWebhookResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('source')
                            ->translateLabel(),
                        TextEntry::make('event')
                            ->translateLabel(),
                        TextEntry::make('url')
                            ->translateLabel(),
                        TextEntry::make('payload')
                            ->translateLabel()
                            ->limit(100),
                    ])
                    ->columns(),
            ]);
    }
}
