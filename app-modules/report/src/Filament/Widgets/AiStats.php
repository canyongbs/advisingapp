<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Livewire\Attributes\On;
use Illuminate\Support\Number;
use AdvisingApp\Ai\Models\PromptUse;
use Illuminate\Support\Facades\Cache;
use AdvisingApp\Ai\Models\PromptUpvote;
use AdvisingApp\Authorization\Models\License;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\Authorization\Enums\LicenseType;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AiStats extends BaseWidget
{
    protected $pagePrefix;

    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public function mount($pagePrefix = '')
    {
        $this->pagePrefix = $pagePrefix;
    }

    #[On('refresh-widgets')]
    public function refreshWidget()
    {
        $this->dispatch('$refresh');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('AI Users', Number::abbreviate(
                Cache::tags([$this->pagePrefix])->remember('ai-users-count', now()->addHours(24), function (): int {
                    return License::where('type', LicenseType::ConversationalAi)->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompts Liked', Number::abbreviate(
                Cache::tags([$this->pagePrefix])->remember('prompts-liked-count', now()->addHours(24), function (): int {
                    return PromptUpvote::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompt Insertions', Number::abbreviate(
                Cache::tags([$this->pagePrefix])->remember('prompts-insertions-count', now()->addHours(24), function (): int {
                    return PromptUse::count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
