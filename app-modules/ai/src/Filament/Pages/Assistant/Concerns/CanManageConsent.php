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

namespace AdvisingApp\Ai\Filament\Pages\Assistant\Concerns;

use App\Filament\Pages\Dashboard;
use Livewire\Attributes\Computed;
use AdvisingApp\Ai\Models\AiThreadFolder;
use AdvisingApp\Consent\Models\ConsentAgreement;
use AdvisingApp\Consent\Enums\ConsentAgreementType;

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
            foreach (AiThreadFolder::defaults() as $default) {
                $user->aiThreadFolders()->create([
                    'application' => static::APPLICATION,
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
