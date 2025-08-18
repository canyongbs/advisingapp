<?php

namespace AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource\Pages;

use AdvisingApp\Project\Filament\Resources\ProjectMilestoneStatusResource;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewProjectMilestoneStatus extends ViewRecord
{
    protected static string $resource = ProjectMilestoneStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('description')
                            ->label('Description'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
