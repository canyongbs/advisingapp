<?php

namespace Assist\Case\Filament\Resources\CaseUpdateResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Assist\Case\Models\CaseUpdate;
use Filament\Resources\Pages\ViewRecord;
use Assist\Case\Enums\CaseUpdateDirection;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Assist\Case\Filament\Resources\CaseItemResource;
use Assist\Case\Filament\Resources\CaseUpdateResource;

class ViewCaseUpdate extends ViewRecord
{
    protected static string $resource = CaseUpdateResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('case.casenumber')
                    ->label('Case')
                    ->translateLabel()
                    ->url(fn (CaseUpdate $caseUpdate): string => CaseItemResource::getUrl('view', ['record' => $caseUpdate]))
                    ->color('primary'),
                IconEntry::make('internal')
                    ->boolean(),
                TextEntry::make('direction')
                    ->icon(fn (CaseUpdateDirection $state): string => match ($state) {
                        CaseUpdateDirection::Inbound => 'heroicon-o-arrow-down-tray',
                        CaseUpdateDirection::Outbound => 'heroicon-o-arrow-up-tray',
                    })
                    ->formatStateUsing(fn (CaseUpdateDirection $state): string => Str::ucfirst($state->value)),
                TextEntry::make('update')
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
