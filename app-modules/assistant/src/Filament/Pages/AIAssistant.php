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

    protected AIInterface $ai;

    public Chat $chat;

    public array $messages = [];

    public string $message = '';

    public function mount()
    {
        $this->ai = app(AIInterface::class);

        $this->chat = new Chat(
            ChatMessage::collection([]),
        );

        $this->messages = $this->chat->messages->toArray();
    }

    public function send(): void
    {
        $this->chat->messages[] = new ChatMessage(
            message: $this->message,
            from: 'user',
        );

        $this->message = '';

        $this->messages = $this->chat->messages->toArray();
    }
}
