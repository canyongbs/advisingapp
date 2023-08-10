<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource\Pages;

use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledgeBaseCategory extends EditRecord
{
    protected static string $resource = KnowledgeBaseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
