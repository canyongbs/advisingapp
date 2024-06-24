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
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('AI Users', Number::abbreviate(
                Cache::tags(['ai-users'])
                    ->remember('ai-users-count', now()->addHour(), function (): int {
                        return License::where('type', LicenseType::ConversationalAi)->count();
                    }),
                maxPrecision: 2,
            )),
            Stat::make('Prompts Liked', Number::abbreviate(
                Cache::tags(['prompts-liked'])
                    ->remember('prompts-liked-count', now()->addHour(), function (): int {
                        return PromptUpvote::count();
                    }),
                maxPrecision: 2,
            )),
            Stat::make('Prompt Insertions', Number::abbreviate(
                Cache::tags(['prompts-insertions'])
                    ->remember('prompts-insertions-count', now()->addHour(), function (): int {
                        return PromptUse::count();
                    }),
                maxPrecision: 2,
            )),
        ];
    }
}
