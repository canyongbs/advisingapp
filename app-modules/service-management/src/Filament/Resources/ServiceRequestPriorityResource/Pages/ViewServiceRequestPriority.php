<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\ServiceManagement\Filament\Resources\ServiceRequestPriorityResource;

class ViewServiceRequestPriority extends ViewRecord
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
