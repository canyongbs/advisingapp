<?php

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class StudentInteractionStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 12,
        'md' => 6,
        'lg' => 6,
    ];

    public function getStats(): array
    {
        return [
            Stat::make('Total Interactions', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-student-interactions-count', now()->addHours(24), function (): int {
                    return Interaction::query()
                        ->whereHasMorph('interactable', Student::class)
                        ->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Students with Interactions', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('students-with-interactions', now()->addHours(24), function (): int {
                    return Interaction::query()
                        ->whereHasMorph('interactable', Student::class)
                        ->distinct('interactable_id')
                        ->count('interactable_id');
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
