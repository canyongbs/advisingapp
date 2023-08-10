<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ViewKnowledgeBaseItem extends ViewRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('question')
                    ->label('Question/Issue/Feature')
                    ->translateLabel(),
                TextEntry::make('solution')
                    ->label('Solution')
                    ->translateLabel(),
                TextEntry::make('notes')
                    ->label('Notes')
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
