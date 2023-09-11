<?php

namespace Assist\Assistant\Filament\Pages;

use Filament\Pages\Page;

class AIAssistant extends Page
{
    protected static ?string $navigationLabel = 'AI Assistant';

    protected static ?string $title = 'AI Assistant';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'assistant::filament.pages.a-i-assistant';
}
