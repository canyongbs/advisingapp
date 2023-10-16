<?php

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use Assist\Audit\Models\Audit;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\Audit\Filament\Resources\AuditResource;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('auditable_type')
                            ->label('Auditable'),
                        TextEntry::make('user.name')
                            ->label('Change Agent (User)')
                            ->placeholder('N/A'),
                        TextEntry::make('event')
                            ->label('CalendarEvent'),
                        TextEntry::make('url')
                            ->label('URL'),
                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                        TextEntry::make('user_agent')
                            ->label('User Agent'),
                        TextEntry::make('getModified')
                            ->label('Changes')
                            ->columnSpanFull()
                            ->state(function (Audit $record) {
                                return $record->getModified();
                            })
                            ->view('filament.infolists.entries.change-entry'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
