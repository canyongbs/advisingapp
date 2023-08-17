<?php

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use OwenIt\Auditing\Models\Audit;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Audit\Filament\Resources\AuditResource;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('auditable_type')
                    ->label('Auditable'),
                TextEntry::make('user.name')
                    ->label('Change Agent (User)')
                    ->placeholder('Never'),
                TextEntry::make('event')
                    ->label('Event'),
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
