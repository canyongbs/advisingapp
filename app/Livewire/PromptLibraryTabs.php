<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace App\Livewire;

use AdvisingApp\Ai\Enums\AiPromptTabs;
use AdvisingApp\Ai\Filament\Pages\Assistant\Concerns\CanManagePromptLibrary;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Ai\Models\Prompt;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class PromptLibraryTabs extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use CanManagePromptLibrary;

    public string $activeTab = AiPromptTabs::Newest->value;

    public ?AiThread $thread;

    public mixed $isSmartPromptsTypePreselected = null;

    public function render(): View
    {
        $prompts = Prompt::query()
            ->where('is_smart', true)
            ->withCount('upvotes', 'uses')
            ->when(
                $this->activeTab === AiPromptTabs::Newest->value,
                fn (Builder $query) => $query->latest()
            )
            ->when(
                $this->activeTab === AiPromptTabs::MostLoved->value,
                fn (Builder $query) => $query->whereHas('upvotes')
                    ->orderByDesc('upvotes_count')
            )
            ->when(
                $this->activeTab === AiPromptTabs::MostViewed->value,
                fn (Builder $query) => $query->whereHas('uses')
                    ->orderByDesc('uses_count')
            )
            ->limit(6)
            ->get();

        return view('livewire.prompt-library-tabs', compact('prompts'));
    }
}
