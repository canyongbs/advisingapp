<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ViewKnowledgeBaseItem extends ViewRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('question')
                            ->label('Question/Issue/Feature')
                            ->translateLabel(),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->translateLabel(),
                        TextEntry::make('quality.name')
                            ->label('Quality')
                            ->translateLabel(),
                        TextEntry::make('category.name')
                            ->label('Category')
                            ->translateLabel(),
                        TextEntry::make('public')
                            ->label('Public')
                            ->translateLabel()
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                        TextEntry::make('division.name')
                            ->label('Division')
                            ->translateLabel(),
                        ViewEntry::make('solution')
                            ->label('Solution')
                            ->translateLabel()
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                        ViewEntry::make('notes')
                            ->label('Notes')
                            ->translateLabel()
                            ->columnSpanFull()
                            ->view('filament.infolists.entries.html'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
