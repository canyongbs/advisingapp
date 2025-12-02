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

namespace AdvisingApp\MeetingCenter\Actions;

use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetAvailableAppointmentSlots
{
    /**
     * @return array<int, array{start: string, end: string}>
     */
    public function __invoke(User $user, int $year, int $month): array
    {
        $period = CarbonPeriod::create(
            Carbon::create($year, $month, 1)->startOfDay(),
            Carbon::create($year, $month, 1)->endOfMonth()->endOfDay()
        );

        assert($period->start instanceof Carbon);
        assert($period->end instanceof Carbon);

        $busyPeriods = $this->getBusyPeriodsFor($user, $period->start, $period->end);

        return $this->buildAvailableBlocksFor($user, $period, $busyPeriods);
    }

    /**
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    protected function getBusyPeriodsFor(User $user, Carbon $start, Carbon $end): Collection
    {
        /** @phpstan-ignore-next-line */
        return (new Collection())
            ->merge($this->getCalendarEventsFor($user, $start, $end))
            /** @phpstan-ignore-next-line */
            ->map(fn (CalendarEvent $event) => [
                'start' => $event->starts_at,
                'end' => $event->ends_at,
            ])
            ->values();
    }

    /**
     * @return Collection<int, CalendarEvent>
     */
    protected function getCalendarEventsFor(User $user, Carbon $start, Carbon $end): Collection
    {
        return CalendarEvent::query()
            ->whereHas('calendar', fn (Builder $query) => $query->whereBelongsTo($user))
            ->where(fn (Builder $query) => $this->wherePeriodOverlaps($query, $start, $end))
            ->get();
    }

    /**
     * @param Builder<CalendarEvent> $query
     */
    protected function wherePeriodOverlaps(Builder $query, Carbon $start, Carbon $end): void
    {
        $query->whereBetween('starts_at', [$start, $end])
            ->orWhereBetween('ends_at', [$start, $end])
            ->orWhere(fn (Builder $subQuery) => $subQuery->where('starts_at', '<=', $start)->where('ends_at', '>=', $end));
    }

    /**
     * @param Collection<int, array{start: Carbon, end: Carbon}> $busyPeriods
     *
     * @return array<int, array{start: string, end: string}>
     */
    protected function buildAvailableBlocksFor(User $user, CarbonPeriod $period, Collection $busyPeriods): array
    {
        /** @var Collection<int, Carbon> $periodCollection */
        $periodCollection = new Collection($period);

        return $periodCollection
            ->reject(fn (Carbon $date) => $this->isOutOfOffice($user, $date))
            ->flatMap(fn (Carbon $date) => $this->getAvailableBlocksForDay($user, $date, $busyPeriods))
            ->all();
    }

    protected function isOutOfOffice(User $user, Carbon $date): bool
    {
        if (! $user->out_of_office_is_enabled) {
            return false;
        }

        if (! $user->out_of_office_starts_at || ! $user->out_of_office_ends_at) {
            return false;
        }

        return $date->between($user->out_of_office_starts_at, $user->out_of_office_ends_at);
    }

    /**
     * @param Collection<int, array{start: Carbon, end: Carbon}> $busyPeriods
     *
     * @return array<int, array{start: string, end: string}>
     */
    protected function getAvailableBlocksForDay(User $user, Carbon $date, Collection $busyPeriods): array
    {
        $dayOfWeek = strtolower($date->format('l'));
        $hours = $this->getHoursForDay($user, $dayOfWeek);

        if ($hours->isEmpty()) {
            return [];
        }

        $now = now();

        return $hours
            ->filter(fn (array $period) => $period['enabled'] ?? false)
            ->flatMap(function (array $period) use ($date, $user, $busyPeriods) {
                // Office hours are stored in UTC, so we parse them as UTC first
                $startTime = $period['start'] ?? $period['starts_at'];
                $endTime = $period['end'] ?? $period['ends_at'];

                $startUtc = Carbon::parse("{$date->toDateString()} {$startTime}", 'UTC');
                $endUtc = Carbon::parse("{$date->toDateString()} {$endTime}", 'UTC');

                // Then convert to user's timezone for the correct day
                $userTimezone = $user->timezone ?? 'UTC';
                $start = $startUtc->setTimezone($userTimezone);
                $end = $endUtc->setTimezone($userTimezone);

                return $this->carveOutBusyPeriods($start, $end, $busyPeriods);
            })
            ->filter(fn (array $block) => $block['end']->isAfter($now))
            ->map(function (array $block) use ($now) {
                // If the block has already started, adjust the start time to now
                $start = $block['start']->isBefore($now) ? $now->copy() : $block['start'];

                return [
                    'start' => $start->toIso8601String(),
                    'end' => $block['end']->toIso8601String(),
                ];
            })
            ->all();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    protected function getHoursForDay(User $user, string $dayOfWeek): Collection
    {
        $officeHours = $this->getHoursFromSettings($user->office_hours_are_enabled, $user->office_hours, $dayOfWeek);

        if ($officeHours->isNotEmpty()) {
            return $officeHours;
        }

        $workingHours = $this->getHoursFromSettings($user->working_hours_are_enabled, $user->working_hours, $dayOfWeek);

        if ($workingHours->isNotEmpty()) {
            return $workingHours;
        }

        /** @var Collection<int, array<string, mixed>> */
        return new Collection();
    }

    /**
     * @param array<string, mixed>|null $hoursSettings
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function getHoursFromSettings(bool $isEnabled, ?array $hoursSettings, string $dayOfWeek): Collection
    {
        if (! $this->areHoursEnabled($isEnabled, $hoursSettings)) {
            /** @var Collection<int, array<string, mixed>> */
            return new Collection();
        }

        $dayHours = $hoursSettings[$dayOfWeek] ?? [];

        if (empty($dayHours)) {
            /** @var Collection<int, array<string, mixed>> */
            return new Collection();
        }

        return $this->formatHoursAsCollection($dayHours);
    }

    /**
     * @param array<string, mixed>|null $hoursSettings
     */
    protected function areHoursEnabled(bool $isEnabled, ?array $hoursSettings): bool
    {
        return $isEnabled && $hoursSettings !== null;
    }

    /**
     * @param array<string, mixed> $dayHours
     *
     * @return Collection<int, array<string, mixed>>
     */
    protected function formatHoursAsCollection(array $dayHours): Collection
    {
        if (isset($dayHours['enabled'])) {
            /** @var Collection<int, array<string, mixed>> */
            return new Collection([$dayHours]);
        }

        /** @var Collection<int, array<string, mixed>> */
        return new Collection($dayHours);
    }

    protected function parseTime(Carbon $date, string $time, ?string $timezone): Carbon
    {
        return Carbon::parse("{$date->toDateString()} {$time}", $timezone ?? 'UTC');
    }

    /**
     * @param Collection<int, array{start: Carbon, end: Carbon}> $busyPeriods
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    protected function carveOutBusyPeriods(Carbon $start, Carbon $end, Collection $busyPeriods): array
    {
        /** @var array<int, array{start: Carbon, end: Carbon}> $blocks */
        $blocks = [['start' => $start, 'end' => $end]];

        foreach ($busyPeriods as $busy) {
            $blocks = $this->splitBlocksAroundBusyPeriod($blocks, $busy);
        }

        return $blocks;
    }

    /**
     * @param array<int, array{start: Carbon, end: Carbon}> $blocks
     * @param array{start: Carbon, end: Carbon} $busy
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    protected function splitBlocksAroundBusyPeriod(array $blocks, array $busy): array
    {
        $busyStart = Carbon::parse($busy['start']);
        $busyEnd = Carbon::parse($busy['end']);

        /** @var Collection<int, array{start: Carbon, end: Carbon}> $blockCollection */
        $blockCollection = new Collection($blocks);

        return $blockCollection
            ->flatMap(fn (array $block) => $this->splitBlockIfOverlaps($block, $busyStart, $busyEnd))
            ->all();
    }

    /**
     * @param array{start: Carbon, end: Carbon} $block
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    protected function splitBlockIfOverlaps(array $block, Carbon $busyStart, Carbon $busyEnd): array
    {
        $blockStart = $block['start'];
        $blockEnd = $block['end'];

        // No overlap - keep the block
        if ($busyEnd->lte($blockStart) || $busyStart->gte($blockEnd)) {
            return [$block];
        }

        // Busy period completely covers block - remove it
        if ($busyStart->lte($blockStart) && $busyEnd->gte($blockEnd)) {
            return [];
        }

        // Busy period in the middle - split into two blocks
        if ($busyStart->gt($blockStart) && $busyEnd->lt($blockEnd)) {
            return [
                ['start' => $blockStart, 'end' => $busyStart->copy()],
                ['start' => $busyEnd->copy(), 'end' => $blockEnd],
            ];
        }

        // Busy period overlaps the start - trim the block
        if ($busyStart->lte($blockStart)) {
            return [['start' => $busyEnd->copy(), 'end' => $blockEnd]];
        }

        // Busy period overlaps the end - trim the block
        return [['start' => $blockStart, 'end' => $busyStart->copy()]];
    }
}
