<?php

namespace Assist\Case\Filament\Resources\CaseItemPriorityResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Filament\Resources\ServiceRequestPriorityResource;

class ViewCaseItemPriority extends ViewRecord
{
    protected static string $resource = ServiceRequestPriorityResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('name')
                    ->label('Name')
                    ->translateLabel(),
                TextEntry::make('order')
                    ->label('Order')
                    ->translateLabel(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
