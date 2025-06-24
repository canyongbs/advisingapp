<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Report\Filament\Widgets;

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class StudentEngagementStats extends StatsOverviewReportWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = [
        'sm' => 2,
        'md' => 4,
        'lg' => 4,
    ];

    public function getStats(): array
    {
        $startDate = filled($this->filters['startDate'] ?? null)
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : null;

        $endDate = filled($this->filters['endDate'] ?? null)
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : null;

        $shouldBypassCache = filled($startDate) || filled($endDate);

        $studentsCount = $shouldBypassCache
            ? Student::query()
                ->when($startDate && $endDate, fn ($q) => $q->whereBetween('created_at_source', [$startDate, $endDate]))
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember('total-students-count', now()->addHours(24), fn () => Student::query()->count());

        $emailsCount = $shouldBypassCache
            ? Engagement::query()
                ->whereHasMorph('recipient', Student::class)
                ->where('channel', NotificationChannel::Email)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-emails-count',
                now()->addHours(24),
                fn () => Engagement::query()
                    ->whereHasMorph('recipient', Student::class)
                    ->where('channel', NotificationChannel::Email)
                    ->count()
            );

        $textsCount = $shouldBypassCache
            ? Engagement::query()
                ->whereHasMorph('recipient', Student::class)
                ->where('channel', NotificationChannel::Sms)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-texts-count',
                now()->addHours(24),
                fn () => Engagement::query()
                    ->whereHasMorph('recipient', Student::class)
                    ->where('channel', NotificationChannel::Sms)
                    ->count()
            );

        $engagementFilter = function (Builder $query) use ($startDate, $endDate): void {
            $query->whereHasMorph('recipient', Student::class)
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at', [$startDate, $endDate])
                );
        };

        $staffCount = $shouldBypassCache
            ? User::query()->whereHas('engagements', $engagementFilter)->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-staff-sending-count',
                now()->addHours(24),
                fn () => User::query()->whereHas('engagements', $engagementFilter)->count()
            );

        return [
            Stat::make('Total Students', Number::abbreviate($studentsCount, maxPrecision: 2)),
            Stat::make('Total Emails Sent', Number::abbreviate($emailsCount, maxPrecision: 2)),
            Stat::make('Total Texts Sent', Number::abbreviate($textsCount, maxPrecision: 2)),
            Stat::make('Count of Staff Sending Engagements', Number::abbreviate($staffCount, maxPrecision: 2)),
        ];
    }
}
