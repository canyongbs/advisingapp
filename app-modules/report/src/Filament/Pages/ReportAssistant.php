<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Report\Filament\Pages;

use App\Models\User;
use App\Enums\Feature;
use Filament\Pages\Page;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use App\Filament\Pages\Dashboard;
use Livewire\Attributes\Computed;
use AdvisingApp\Assistant\Models\AssistantChat;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Report\Client\AIReportChatClient;
use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Assistant\Models\AssistantChatFolder;
use AdvisingApp\IntegrationAI\Exceptions\ContentFilterException;
use AdvisingApp\IntegrationAI\Exceptions\TokensExceededException;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use AdvisingApp\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\Chat;
use AdvisingApp\Assistant\Services\AIInterface\DataTransferObjects\ChatMessage;

/**
 * @property EloquentCollection $chats
 */
class ReportAssistant extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'report::filament.pages.report-assistant';

    protected static ?string $navigationGroup = 'Reporting';

    protected static ?int $navigationSort = 40;

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

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        if (! $user->can(Feature::ExperimentalReporting->getGateName())) {
            return false;
        }

        return $user->can('report.access_assistant');
    }

    public function mount(): void
    {
        $this->consentAgreement = ConsentAgreement::where('type', ConsentAgreementType::AzureOpenAI)->first();

        /** @var AssistantChat $chat */
        $chat = $this->chats->first();

        $this->chat = new Chat(
            id: $chat?->id ?? null,
            messages: ChatMessage::collection($chat?->messages ?? []),
        );
    }

    #[Computed]
    public function chats(): EloquentCollection
    {
        /** @var User $user */
        $user = auth()->user();

        return $user
            ->assistantChats()
            ->doesntHave('folder')
            ->latest()
            ->get();
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

        if (! $user->defaultAssistantChatFoldersHaveBeenCreated()) {
            foreach (AssistantChatFolder::defaults() as $default) {
                $user->assistantChatFolders()->create([
                    'name' => $default,
                ]);
            }

            $user->update([
                'default_assistant_chat_folders_created' => true,
            ]);
        }

        $this->dispatch('close-modal', id: 'consent-agreement');
    }

    public function denyConsent(): void
    {
        $this->redirect(Dashboard::getUrl());
    }

    public function sendMessage(): void
    {
        $this->showCurrentResponse = true;

        $this->reset('renderError');
        $this->reset('error');

        $this->validate();

        $this->prompt = $this->message;

        $this->message = '';

        $this->setMessage(nl2br($this->prompt), AIChatMessageFrom::User);

        $this->js('$wire.ask()');
    }

    #[On('ask')]
    public function ask(AIReportChatClient $ai): void
    {
        try {
            $this->currentResponse = $ai->ask($this->chat, function (string $partial) {
                $this->stream('currentResponse', nl2br($partial));
            });
        } catch (ContentFilterException | TokensExceededException $e) {
            $this->renderError = true;
            $this->error = $e->getMessage();
        }

        $this->reset('showCurrentResponse');

        if ($this->renderError === false) {
            $this->setMessage($this->currentResponse, AIChatMessageFrom::Assistant);
        }

        $this->reset('currentResponse');
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
