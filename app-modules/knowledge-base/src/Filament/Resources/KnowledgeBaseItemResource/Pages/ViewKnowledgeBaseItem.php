<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use FilamentTiptapEditor\Facades\TiptapConverter;
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
                TextEntry::make('institution.name')
                    ->label('Institution')
                    ->translateLabel(),
                TextEntry::make('solution')
                    ->label('Solution')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->formatStateUsing(fn (string $state): string => TiptapConverter::asHTML($state))
                    ->html(),
                TextEntry::make('notes')
                    ->label('Notes')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->html(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
