<?php

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Assist\Assistant\Models\AssistantChat;
use Assist\Assistant\Services\AIInterface\Contracts\AIInterface;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

class AIAssistant extends Page
{
    protected static ?string $navigationLabel = 'AI Assistant';

    protected static ?string $title = 'AI Assistant';

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'assistant::filament.pages.ai-assistant';

    public Chat $chat;

    #[Rule(['required', 'string'])]
    public string $message = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var AssistantChat $chat */
        $chat = $user->assistantChats()->latest()->first();

        $this->chat = new Chat(
            id: $chat?->id ?? null,
            messages: ChatMessage::collection($chat?->messages ?? []),
        );
    }

    public function saveCurrentMessage(): void
    {
        $this->validate();

        $this->setMessage($this->message, AIChatMessageFrom::User);

        $this->message = '';

        $this->dispatch('current-message-saved');
    }

    #[On('ask')]
    public function ask(): void
    {
        $ai = app(AIInterface::class);

        $response = $ai->ask($this->chat);

        $this->setMessage($response, AIChatMessageFrom::Assistant);
    }

    public function save(): void
    {
        if (filled($this->chat->id)) {
            return;
        }

        /** @var User $user */
        $user = auth()->user();

        /** @var AssistantChat $assistantChat */
        $assistantChat = $user->assistantChats()->create();

        $this->chat->messages->each(function (ChatMessage $message) use ($assistantChat) {
            $assistantChat->messages()->create($message->toArray());
        });

        $this->chat->id = $assistantChat->id;
    }

    protected function setMessage(string $message, AIChatMessageFrom $from): void
    {
        if ($this->chat->id) {
            /** @var User $user */
            $user = auth()->user();

            /** @var AssistantChat $assistantChat */
            $assistantChat = $user->assistantChats()->findOrFail($this->chat->id);

            $assistantChat->messages()->create([
                'message' => $message,
                'from' => $from,
            ]);
        }

        $this->chat->messages[] = new ChatMessage(
            message: $message,
            from: $from,
        );
    }
}
