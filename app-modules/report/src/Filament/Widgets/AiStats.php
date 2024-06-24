<?php

namespace AdvisingApp\Report\Filament\Widgets;

use Illuminate\Support\Number;
use AdvisingApp\Ai\Models\PromptUse;
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
                License::where('type', LicenseType::ConversationalAi)->count(),
                maxPrecision: 2
            )),
            Stat::make('Prompts Liked', Number::abbreviate(
                PromptUpvote::count(),
                maxPrecision: 2,
            )),
            Stat::make('Prompt Insertions', Number::abbreviate(
                PromptUse::count(),
                maxPrecision: 2,
            )),
        ];
    }
}
