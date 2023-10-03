<?php

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Livewire\Attributes\Rule;
use App\Filament\Pages\Dashboard;
use Filament\Forms\Components\TextInput;
use Assist\Assistant\Models\AssistantChat;
use Assist\Consent\Models\ConsentAgreement;
use Illuminate\Database\Eloquent\Collection;
use Assist\Consent\Enums\ConsentAgreementType;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\IntegrationAI\Exceptions\ContentFilterException;
use Assist\IntegrationAI\Exceptions\TokensExceededException;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

class PersonalAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'assistant::filament.pages.ai-assistant';

    protected static ?string $navigationGroup = 'Productivity Tools';

    protected static ?int $navigationSort = 1;

    public Collection $chats;

    public Chat $chat;

    #[Rule(['required', 'string'])]
    public string $message = '';

    public string $prompt = '';

    public bool $showCurrentResponse = false;

    public string $currentResponse = '';

    public bool $renderError = false;

    public string $error = '';

    public ConsentAgreement $consentAgreement;

    public bool $consentedToTerms = false;

    public bool $loading = true;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('assistant.access');
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->authorize('assistant.access');

        $this->consentAgreement = ConsentAgreement::query()
            ->where('type', ConsentAgreementType::AzureOpenAI)
            ->first();

        $this->chats = $user->assistantChats()->latest()->get();

        /** @var AssistantChat $chat */
        $chat = $this->chats->first();

        $this->chat = new Chat(
            id: $chat?->id ?? null,
            messages: ChatMessage::collection($chat?->messages ?? []),
        );
    }

    public function determineIfConsentWasGiven(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->hasNotConsentedTo($this->consentAgreement)) {
            $this->dispatch('open-modal', id: 'consent-agreement');
        } else {
            $this->consentedToTerms = true;
        }

        $this->loading = false;
    }

    public function confirmConsent(): void
    {
        /** @var User $user */
        $user = auth()->user();

        if ($this->consentedToTerms === false) {
            return;
        }

        $user->consentTo($this->consentAgreement);

        $this->dispatch('close-modal', id: 'consent-agreement');
    }

    public function denyConsent(): void
    {
        $this->redirect(Dashboard::getUrl());
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
        try {
            $this->currentResponse = $ai->ask($this->chat, function (string $partial) {
                $this->stream('currentResponse', nl2br($partial));
            });
        } catch (ContentFilterException|TokensExceededException $e) {
            $this->renderError = true;
            $this->error = $e->getMessage();
        }

        $this->reset('showCurrentResponse');

        if ($this->renderError === false) {
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

        $this->chats->prepend($assistantChat);
    }

    public function saveChatAction(): Action
    {
        return Action::make('saveChat')
            ->form(
                [
                    TextInput::make('name')
                        ->label('Name')
                        ->placeholder('Name this chat')
                        ->required(),
                ]
            )
            ->action(function (array $data) {
                ray($data);

                if (filled($this->chat->id)) {
                    return;
                }

                /** @var User $user */
                $user = auth()->user();

                /** @var AssistantChat $assistantChat */
                $assistantChat = $user->assistantChats()->create(['name' => $data['name']]);

                $this->chat->messages->each(function (ChatMessage $message) use ($assistantChat) {
                    $assistantChat->messages()->create($message->toArray());
                });

                $this->chat->id = $assistantChat->id;

                $this->chats->prepend($assistantChat);
            });
    }

    public function selectChat(AssistantChat $chat): void
    {
        $this->reset(['message', 'prompt', 'renderError', 'error']);

        $this->chat = new Chat(
            id: $chat->id ?? null,
            messages: ChatMessage::collection($chat->messages ?? []),
        );
    }

    public function newChat(): void
    {
        $this->reset(['message', 'prompt', 'renderError', 'error']);

        $this->chat = new Chat(id: null, messages: ChatMessage::collection([]));
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
