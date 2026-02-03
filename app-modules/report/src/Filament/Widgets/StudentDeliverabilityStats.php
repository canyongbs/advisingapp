<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class StudentDeliverabilityStats extends StatsOverviewReportWidget
{
    protected int | string | array $columnSpan = 'full';

    public function getStats(): array
    {
        $startDate = $this->getStartDate();
        $endDate = $this->getEndDate();
        $groupId = $this->getSelectedGroup();

        $shouldBypassCache = filled($startDate) || filled($endDate) || filled($groupId);

        $totalStudents = $shouldBypassCache
            ? Student::query()
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
                )
                ->when(
                    $groupId,
                    fn (Builder $query) => $this->groupFilter($query, $groupId)
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'total-students-count',
                now()->addHours(24),
                fn () => Student::query()->count()
            );

        $studentsPrimaryEmailMissing = $shouldBypassCache
            ? Student::query()
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
                )
                ->when(
                    $groupId,
                    fn (Builder $query) => $this->groupFilter($query, $groupId)
                )
                ->whereNull('primary_email_id')
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'missing-email-students-count',
                now()->addHours(24),
                fn () => Student::query()->whereNull('primary_email_id')->count()
            );

        $studentsPrimaryEmailUnhealthy = $shouldBypassCache
            ? Student::query()
                ->where(function (Builder $query) {
                    $query->whereDoesntHave('primaryEmailAddress')
                        ->orWhereHas('primaryEmailAddress', function (Builder $query1) {
                            $query1->where(function (Builder $query2) {
                                $query2->has('bounced')
                                    ->orWhereHas('optedOut', function (Builder $query3) {
                                        $query3->where('status', EmailAddressOptInOptOutStatus::OptedOut);
                                    });
                            });
                        });
                })
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
                )
                ->when(
                    $groupId,
                    fn (Builder $query) => $this->groupFilter($query, $groupId)
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'unhealthy-email-students-count',
                now()->addHours(24),
                fn () => Student::query()
                    ->where(function (Builder $query) {
                        $query->whereDoesntHave('primaryEmailAddress')
                            ->orWhereHas('primaryEmailAddress', function (Builder $query1) {
                                $query1->where(function (Builder $query2) {
                                    $query2->has('bounced')
                                        ->orWhereHas('optedOut', function (Builder $query3) {
                                            $query3->where('status', EmailAddressOptInOptOutStatus::OptedOut);
                                        });
                                });
                            });
                    })->count()
            );

        $studentsPrimaryPhoneMissing = $shouldBypassCache
            ? Student::query()
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
                )
                ->when(
                    $groupId,
                    fn (Builder $query) => $this->groupFilter($query, $groupId)
                )
                ->whereNull('primary_phone_id')
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'missing-phone-students-count',
                now()->addHours(24),
                fn () => Student::query()->whereNull('primary_phone_id')->count()
            );

        $studentsPrimaryPhoneUnhealthy = $shouldBypassCache
            ? Student::query()
                ->where(function (Builder $query) {
                    $query->whereHas('primaryPhoneNumber', function (Builder $query1) {
                        $query1->where(function (Builder $query2) {
                            $query2->where('can_receive_sms', false)
                                ->orWhereHas('smsOptOut');
                        });
                    })->orWhereDoesntHave('primaryPhoneNumber');
                })
                ->when(
                    $startDate && $endDate,
                    fn (Builder $query): Builder => $query->whereBetween('created_at_source', [$startDate, $endDate])
                )
                ->when(
                    $groupId,
                    fn (Builder $query) => $this->groupFilter($query, $groupId)
                )
                ->count()
            : Cache::tags(["{{$this->cacheTag}}"])->remember(
                'unhealthy-phone-students-count',
                now()->addHours(24),
                fn () => Student::query()->where(function (Builder $query) {
                    $query->whereHas('primaryPhoneNumber', function (Builder $query1) {
                        $query1->where(function (Builder $query2) {
                            $query2->where('can_receive_sms', false)
                                ->orWhereHas('smsOptOut');
                        });
                    })->orWhereDoesntHave('primaryPhoneNumber');
                })->count()
            );

        $primaryEmailMissingPercentage = $totalStudents > 0 ? ($studentsPrimaryEmailMissing / $totalStudents) * 100 : 0;
        $primaryEmailUnhealthyPercentage = $totalStudents > 0 ? ($studentsPrimaryEmailUnhealthy / $totalStudents) * 100 : 0;
        $primaryPhoneMissingPercentage = $totalStudents > 0 ? ($studentsPrimaryPhoneMissing / $totalStudents) * 100 : 0;
        $primaryPhoneUnhealthyPercentage = $totalStudents > 0 ? ($studentsPrimaryPhoneUnhealthy / $totalStudents) * 100 : 0;

        return [
            Stat::make(
                'Primary Email Missing',
                Number::format($primaryEmailMissingPercentage, 2) . '%'
            ),
            Stat::make(
                'Primary Email Unhealthy',
                Number::format($primaryEmailUnhealthyPercentage, 2) . '%'
            ),
            Stat::make(
                'Primary Phone Missing',
                Number::format($primaryPhoneMissingPercentage, 2) . '%'
            ),
            Stat::make(
                'Primary Phone Unhealthy',
                Number::format($primaryPhoneUnhealthyPercentage, 2) . '%'
            ),
        ];
    }

    protected function getHeading(): ?string
    {
        return 'Student Messaging Issue Summary';
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
