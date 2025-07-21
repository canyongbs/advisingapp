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
use Livewire\Component;

class PromptTabs extends Component implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use CanManagePromptLibrary;

    public string $activeTab = AiPromptTabs::Newest->value;

    public AiThread $thread;

    public function triggerPromptLibraryAction(): void
    {
        $this->mountAction('insertFromPromptLibrary');
    }

    public function render(): View
    {
        $prompts = Prompt::query()
            ->withCount('upvotes', 'uses')
            ->when($this->activeTab === AiPromptTabs::Newest->value, fn ($q) => $q->latest())
            ->when($this->activeTab === AiPromptTabs::MostLoved->value, fn ($q) => $q->orderByDesc('upvotes_count'))
            ->when($this->activeTab === AiPromptTabs::MostViewed->value, fn ($q) => $q->orderByDesc('uses_count'))
            ->limit(6)
            ->get();

        return view('livewire.prompt-tabs', compact('prompts'));
    }
}
