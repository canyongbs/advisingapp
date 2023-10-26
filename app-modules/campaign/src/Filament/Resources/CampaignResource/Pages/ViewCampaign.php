<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Assist\Campaign\Models\Campaign;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\Campaign\Filament\Resources\CampaignResource;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        // TODO Make link to caseload
                        TextEntry::make('caseload.name')
                            ->label('Caseload'),
                        TextEntry::make('execute_at')
                            ->dateTime(),
                        IconEntry::make('execute_at')
                            ->label('Has Been Executed?')
                            ->getStateUsing(fn (Campaign $record) => $record->hasBeenExecuted())
                            ->boolean(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->hidden(fn (Campaign $record) => $record->hasBeenExecuted() === true),
        ];
    }
}
