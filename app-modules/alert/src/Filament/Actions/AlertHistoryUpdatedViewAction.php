<?php

namespace AdvisingApp\Alert\Filament\Actions;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\Section;
use AdvisingApp\Alert\Histories\AlertHistory;
use Filament\Infolists\Components\KeyValueEntry;

class AlertHistoryUpdatedViewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->infolist([
            Section::make()
                ->schema([
                    KeyValueEntry::make('Changes')
                        ->getStateUsing(fn (AlertHistory $record) => $record->formatted)
                        ->view('filament.infolists.components.update-entry'),
                ]),
        ]);
    }
}
