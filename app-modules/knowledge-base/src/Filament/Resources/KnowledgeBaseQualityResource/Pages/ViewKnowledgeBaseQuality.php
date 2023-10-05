<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;

class ViewKnowledgeBaseQuality extends ViewRecord
{
    protected static string $resource = KnowledgeBaseQualityResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                Section::make()
                    ->schema([
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
