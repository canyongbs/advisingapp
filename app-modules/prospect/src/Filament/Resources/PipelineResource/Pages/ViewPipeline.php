<?php

namespace AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource;

class ViewPipeline extends ViewRecord
{
    protected static string $resource = PipelineResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make()->schema([
                TextEntry::make('name'),
                TextEntry::make('description'),
                TextEntry::make('segment.name'),
                RepeatableEntry::make('stages')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Stage'),
                        IconEntry::make('is_default')
                            ->label('Is Default?')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
