<?php

namespace Assist\Case\Filament\Resources\CaseItemResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Filament\Resources\CaseItemResource;

class ViewCaseItem extends ViewRecord
{
    protected static string $resource = CaseItemResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('casenumber')
                    ->label('Case Number')
                    ->translateLabel(),
                TextEntry::make('institution.name')
                    ->label('Institution')
                    ->translateLabel(),
                TextEntry::make('state.name')
                    ->label('State')
                    ->translateLabel(),
                TextEntry::make('priority.name')
                    ->label('Priority')
                    ->translateLabel(),
                TextEntry::make('type.name')
                    ->label('Type')
                    ->translateLabel(),
                TextEntry::make('close_details')
                    ->label('Close Details/Description')
                    ->translateLabel()
                    ->columnSpanFull(),
                TextEntry::make('res_details')
                    ->label('Internal Case Details')
                    ->translateLabel()
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
