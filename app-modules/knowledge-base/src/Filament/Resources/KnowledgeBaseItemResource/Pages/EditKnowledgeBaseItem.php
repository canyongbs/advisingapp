<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeBaseItem extends EditRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
