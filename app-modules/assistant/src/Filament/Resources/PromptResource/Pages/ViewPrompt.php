<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use AdvisingApp\Assistant\Models\Prompt;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use AdvisingApp\Assistant\Filament\Resources\PromptTypeResource;

class ViewPrompt extends ViewRecord
{
    protected static string $resource = PromptResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns()
                    ->schema([
                        TextEntry::make('title'),
                        TextEntry::make('type.title')
                            ->url(fn (Prompt $record) => PromptTypeResource::getUrl('view', ['record' => $record->type])),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                        TextEntry::make('prompt')
                            ->columnSpanFull(),
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
