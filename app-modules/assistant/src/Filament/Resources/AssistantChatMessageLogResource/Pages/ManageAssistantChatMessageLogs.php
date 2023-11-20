<?php

namespace Assist\Assistant\Filament\Resources\AssistantChatMessageLogResource\Pages;

use Filament\Resources\Pages\ManageRecords;
use Assist\Assistant\Filament\Resources\AssistantChatMessageLogResource;

class ManageAssistantChatMessageLogs extends ManageRecords
{
    protected static string $resource = AssistantChatMessageLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
