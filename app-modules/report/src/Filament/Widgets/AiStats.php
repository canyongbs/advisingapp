<?php

namespace AdvisingApp\Report\Filament\Widgets;

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

    protected function getStats(): array
    {
        return [
            Stat::make('AI Users', Number::abbreviate(
                Cache::tags([$this->pagePrefix])->rememberForever('ai-users-count', function (): int {
                    Cache::forget($this->pagePrefix . '-updated-time');
                    Cache::add($this->pagePrefix . '-updated-time', now(auth()->user()->timezone));

                    return License::where('type', LicenseType::ConversationalAi)->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompts Liked', Number::abbreviate(
                Cache::tags([$this->pagePrefix])->rememberForever('prompts-liked-count', function (): int {
                    return PromptUpvote::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompt Insertions', Number::abbreviate(
                Cache::tags([$this->pagePrefix])->rememberForever('prompts-insertions-count', function (): int {
                    return PromptUse::count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
