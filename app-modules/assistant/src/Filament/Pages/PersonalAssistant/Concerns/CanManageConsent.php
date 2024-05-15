<?php

namespace AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns;

use App\Filament\Pages\Dashboard;
use Livewire\Attributes\Computed;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Consent\Enums\ConsentAgreementType;
use AdvisingApp\Assistant\Models\AssistantChatFolder;

trait CanManageConsent
{
    #[Computed]
    public function consentAgreement(): ConsentAgreement
    {
        return ConsentAgreement::query()
            ->where('type', ConsentAgreementType::AzureOpenAI)
            ->first();
    }

    #[Computed]
    public function isConsented(): bool
    {
        return auth()->user()->hasConsentedTo($this->consentAgreement);
    }

    public function confirmConsent(): void
    {
        $user = auth()->user();

        if ($this->isConsented) {
            return;
        }

        $user->consentTo($this->consentAgreement);

        unset($this->isConsented);

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
}
