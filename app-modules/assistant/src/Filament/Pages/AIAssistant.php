<?php

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use App\Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Redirect;
use Assist\Assistant\Models\AssistantChat;
use Assist\Consent\Models\ConsentAgreement;
use Assist\Consent\Enums\ConsentAgreementType;
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

    public ConsentAgreement $consentAgreement;

    public bool $consentedToTerms = false;

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $this->consentAgreement = ConsentAgreement::where('type', ConsentAgreementType::AZURE_OPEN_AI)->first();

        if ($user->hasNotConsentedTo($this->consentAgreement)) {
            ray('user has not consented...');
            $this->consentedToTerms = false;
            $this->dispatch('open-modal', id: 'consent-agreement');
        } else {
            $this->consentedToTerms = true;
            ray('user has consented...');
        }

        /** @var AssistantChat $chat */
        $chat = $user->assistantChats()->latest()->first();

        $this->chat = new Chat(
            id: $chat?->id ?? null,
            messages: ChatMessage::collection($chat?->messages ?? []),
        );
    }

    public function confirmConsent(): void
    {
        ray('confirmConsent()');

        // TODO Real validation
        if ($this->consentedToTerms === false) {
            return;
        }

        auth()->user()->consentTo($this->consentAgreement);

        $this->consentedToTerms = true;

        $this->dispatch('close-modal', id: 'consent-agreement');
    }

    public function denyConsent()
    {
        return Redirect::to(Dashboard::getUrl());
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
                $this->stream('currentResponse', $partial);
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
