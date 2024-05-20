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

namespace AdvisingApp\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Actions\CreateThread;
use AdvisingApp\Ai\Actions\DeleteThread;
use AdvisingApp\Assistant\Models\AiAssistant;
use AdvisingApp\Authorization\Enums\LicenseType;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns\CanManageConsent;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns\CanManageFolders;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns\CanManageThreads;
use AdvisingApp\Assistant\Filament\Pages\PersonalAssistant\Concerns\CanManagePromptLibrary;

/**
 * @property EloquentCollection $chats
 */
class PersonalAssistant extends Page
{
    use CanManageConsent;
    use CanManageFolders;
    use CanManagePromptLibrary;
    use CanManageThreads;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'assistant::filament.pages.personal-assistant';

    protected static ?string $navigationGroup = 'Artificial Intelligence';

    protected static ?int $navigationSort = 10;

    #[Locked]
    public ?AiThread $thread = null;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        return $user->can('assistant.access');
    }

    public function createThread(?AiAssistant $assistant = null): void
    {
        $this->thread = app(CreateThread::class)($assistant);
    }

    #[Computed]
    public function threadsWithoutAFolder(): EloquentCollection
    {
        return auth()->user()
            ->aiThreads()
            ->whereRelation('assistant', 'application', AiApplication::PersonalAssistant)
            ->whereNotNull('name')
            ->doesntHave('folder')
            ->latest()
            ->get();
    }

    public function loadFirstThread(): void
    {
        $this->selectThread($this->threadsWithoutAFolder->first());

        if ($this->thread) {
            return;
        }

        $this->createThread();
    }

    public function selectThread(?AiThread $thread): void
    {
        if (! $thread) {
            return;
        }

        if (
            $this->thread &&
            blank($this->thread->name) &&
            (! $this->thread->messages()->exists())
        ) {
            app(DeleteThread::class)($this->thread);
        }

        if (! $thread->user()->is(auth()->user())) {
            abort(404);
        }

        $this->thread = $thread;
    }
}
