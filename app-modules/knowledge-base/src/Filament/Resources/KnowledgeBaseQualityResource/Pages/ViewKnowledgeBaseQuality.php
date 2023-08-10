<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource\Pages;

use Filament\Actions;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseQualityResource;

class ViewKnowledgeBaseQuality extends ViewRecord
{
    protected static string $resource = KnowledgeBaseQualityResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return parent::infolist($infolist)
            ->schema([
                TextEntry::make('id')
                    ->label('ID')
                    ->translateLabel(),
                TextEntry::make('name')
                    ->label('Name')
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
