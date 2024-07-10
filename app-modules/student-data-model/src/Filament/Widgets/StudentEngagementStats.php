<?php

namespace AdvisingApp\StudentDataModel\Filament\Widgets;

use App\Models\User;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Cache;
use Filament\Widgets\StatsOverviewWidget\Stat;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Report\Filament\Widgets\StatsOverviewReportWidget;

class StudentEngagementStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    protected function getStats(): array
    {
        return [
            Stat::make('Total Students', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-students-count', now()->addHours(24), function (): int {
                    return Student::count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Total Emails Sent', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-emails-count', now()->addHours(24), function (): int {
                    return EngagementDeliverable::whereHas('engagement', function ($q) {
                        return $q->whereHasMorph('recipient', Student::class);
                    })
                        ->where('channel', 'email')
                        ->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Total Texts Sent', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-texts-count', now()->addHours(24), function (): int {
                    return EngagementDeliverable::whereHas('engagement', function ($q) {
                        return $q->whereHasMorph('recipient', Student::class);
                    })
                        ->where('channel', 'sms')
                        ->count();
                }),
                maxPrecision: 2,
            )),
            Stat::make('Count of Staff Sending Enagements', Number::abbreviate(
                Cache::tags([$this->cacheTag])->remember('total-staff-sending-count', now()->addHours(24), function (): int {
                    return User::whereHas('engagements', function ($q) {
                        return $q->whereHasMorph('recipient', Student::class);
                    })->count();
                }),
                maxPrecision: 2,
            )),
        ];
    }
}
