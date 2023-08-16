<?php

namespace Assist\Audit\Filament\Resources\AuditResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
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
                    ->label('Change Agent (User)'),
                TextEntry::make('event')
                    ->label('Event'),
                TextEntry::make('old_values')
                    ->label('Old Values'),
                TextEntry::make('new_values')
                    ->label('New Values'),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
