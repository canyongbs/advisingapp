<?php

namespace Assist\Case\Filament\Resources\CaseItemStatusResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Case\Models\CaseItemStatus;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Filament\Resources\CaseItemStatusResource;

class ViewCaseItemStatus extends ViewRecord
{
    protected static string $resource = CaseItemStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('name')
                    ->label('Name')
                    ->translateLabel(),
                TextEntry::make('color')
                    ->label('Color')
                    ->translateLabel()
                    ->badge()
                    ->color(fn (CaseItemStatus $caseItemStatus) => $caseItemStatus->color),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
