<?php

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Assist\Assistant\Models\AssistantChat;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Exceptions\ContentFilterException;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
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

    public string $prompt = '';

    public bool $showCurrentResponse = false;

    public string $currentResponse = '';

    public bool $renderError = false;

    public string $error = '';

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

    public function sendMessage(): void
    {
        $this->reset('renderError');
        $this->reset('error');

        $this->validate();

        $this->prompt = $this->message;

        $this->message = '';

        $this->setMessage($this->prompt, AIChatMessageFrom::User);

        $this->js('$wire.ask()');
    }

    #[On('ask')]
    public function ask(AIChatClient $ai): void
    {
        // TODO Figure out why setting this value in the ask() method
        // Does not result in the frontend reflecting the changes.
        // $this->showCurrentResponse = true;

        try {
            $this->currentResponse = $ai->ask($this->chat, function (string $partial) {
                $this->stream('currentResponse', $partial);
            });
        } catch (ContentFilterException|TokensExceededException $e) {
            $this->renderError = true;
            $this->error = $e->getMessage();
        }

        $this->reset('showCurrentResponse');

        if ($this->error === false) {
            $this->setMessage($this->currentResponse, AIChatMessageFrom::Assistant);
        }

        $this->reset('currentResponse');
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
        if (filled($this->chat->id)) {
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
