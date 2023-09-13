<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseStatusResource;

class ViewKnowledgeBaseStatus extends ViewRecord
{
    protected static string $resource = KnowledgeBaseStatusResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->translateLabel(),
                        TextEntry::make('name')
                            ->label('Name')
                            ->translateLabel(),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
