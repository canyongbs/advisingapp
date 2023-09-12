<?php

namespace Assist\Assistant\Filament\Pages;

use Filament\Pages\Page;
use Assist\Assistant\Services\AIInterface\Contracts\AIInterface;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

class AIAssistant extends Page
{
    protected static ?string $navigationLabel = 'AI Assistant';

    protected static ?string $title = 'AI Assistant';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'assistant::filament.pages.a-i-assistant';

    public Chat $chat;

    public string $message = '';

    public function mount()
    {
        $this->chat = new Chat(
            ChatMessage::collection([]),
        );
    }

    public function send(): void
    {
        $ai = app(AIInterface::class);

        $this->chat->messages[] = new ChatMessage(
            message: $this->message,
            from: 'user',
        );

        $this->message = '';

        $response = $ai->ask($this->chat);

        $this->chat->messages[] = new ChatMessage(
            message: $response,
            from: 'assistant',
        );
    }
}
