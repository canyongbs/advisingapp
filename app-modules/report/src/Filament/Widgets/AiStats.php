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
    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('AI Users', Number::abbreviate(
                Cache::remember('ai-users-count', now()->addMinute(15), function (): int {
                    return License::where('type', LicenseType::ConversationalAi)->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompts Liked', Number::abbreviate(
                Cache::remember('prompts-liked-count', now()->addMinute(15), function (): int {
                    return PromptUpvote::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Prompt Insertions', Number::abbreviate(
                Cache::remember('prompts-insertions-count', now()->addMinute(15), function (): int {
                    return PromptUse::count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
