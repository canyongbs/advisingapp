<?php

namespace AdvisingApp\Alert\Filament\Actions;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Alert\Histories\AlertHistory;

class AlertHistoryCreatedViewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->infolist([
            Section::make()
                ->schema([
                    TextEntry::make('description')
                        ->label(fn (AlertHistory $record): ?string => $record->formatted['description']['key'])
                        ->getStateUsing(fn (AlertHistory $record): ?string => $record->formatted['description']['new']),
                    TextEntry::make('severity')
                        ->label(fn (AlertHistory $record): ?string => $record->formatted['severity']['key'])
                        ->getStateUsing(fn (AlertHistory $record): ?string => $record->formatted['severity']['new']),
                    TextEntry::make('suggested_intervention')
                        ->label(fn (AlertHistory $record): ?string => $record->formatted['suggested_intervention']['key'])
                        ->getStateUsing(fn (AlertHistory $record): ?string => $record->formatted['suggested_intervention']['new']),
                    TextEntry::make('status')
                        ->label(fn (AlertHistory $record): ?string => $record->formatted['status']['key'])
                        ->getStateUsing(fn (AlertHistory $record): ?string => $record->formatted['status']['new']),
                ])
                ->columns(),
        ]);
    }
}
