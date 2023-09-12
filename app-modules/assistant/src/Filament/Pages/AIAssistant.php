<?php

namespace Assist\Assistant\Filament\Pages;

use Filament\Pages\Page;
use Livewire\Attributes\On;
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

    public function saveCurrentMessage()
    {
        $this->chat->messages[] = new ChatMessage(
            message: $this->message,
            from: 'user',
        );

        $this->message = '';

        $this->dispatch('current-message-saved');
    }

    #[On('ask')]
    public function ask()
    {
        $ai = app(AIInterface::class);

        $response = $ai->ask($this->chat);

        $this->chat->messages[] = new ChatMessage(
            message: $response,
            from: 'assistant',
        );
    }
}
