<?php

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

    public bool $isSmartPromptsTypePreselected = false;

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
