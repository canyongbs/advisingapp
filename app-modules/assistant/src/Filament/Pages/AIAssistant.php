<?php

namespace Assist\Assistant\Filament\Pages;

use Filament\Pages\Page;
use Assist\Assistant\Models\AssistantChat;

class AIAssistant extends Page
{
    protected static ?string $navigationLabel = 'AI Assistant';

    protected static ?string $title = 'AI Assistant';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'assistant::filament.pages.a-i-assistant';

    public AssistantChat $chat;

    public array $messages = [];

    public string $message = '';

    public function mount()
    {
        $this->chat = AssistantChat::firstOrCreate([
            'user_id' => auth()->id(),
        ]);

        $this->messages = $this->chat->messages->toArray();
    }

    public function send(): void
    {
        $this->chat->messages()->create([
            'message' => $this->message,
            'from' => 'user',
        ]);

        $this->message = '';

        $this->messages = $this->chat->messages->toArray();
    }
}
