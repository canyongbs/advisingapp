<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Actions;

use AdvisingApp\MeetingCenter\Enums\EventTransparency;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetAvailableGroupAppointmentSlots
{
    /**
     * @return array<int, array{start: string, end: string}>
     */
    public function __invoke(BookingGroup $bookingGroup, int $year, int $month, ?User $forMember = null): array
    {
        if ($forMember) {
            $members = collect([$forMember]);
        } else {
            $members = $bookingGroup->allMembers();
        }

        if ($members->isEmpty()) {
            return [];
        }

        $groupHours = $bookingGroup->available_appointment_hours;

        if (empty($groupHours)) {
            return [];
        }

        $bufferBefore = $bookingGroup->is_default_appointment_buffer_enabled
            ? $bookingGroup->default_appointment_buffer_before_duration
            : 0;

        $bufferAfter = $bookingGroup->is_default_appointment_buffer_enabled
            ? $bookingGroup->default_appointment_buffer_after_duration
            : 0;

        $resolveEffectiveLeadTime = function (BookingGroup $bookingGroup, Collection $members): int {
            $memberMaxLeadTime = PersonalBookingPage::query()
                ->whereIn('user_id', $members->pluck('id'))
                ->max('minimum_booking_lead_time_hours') ?? 0;

            return max($bookingGroup->minimum_booking_lead_time_hours ?? 0, $memberMaxLeadTime);
        };

        $effectiveLeadTime = $resolveEffectiveLeadTime($bookingGroup, $members);

        $memberMaxLeadTimeDays = PersonalBookingPage::query()
            ->whereIn('user_id', $members->pluck('id'))
            ->max('maximum_booking_lead_time_days') ?? 0;
        $effectiveMaxLeadTimeDays = max($bookingGroup->maximum_booking_lead_time_days ?? 0, $memberMaxLeadTimeDays);

        $latestAllowed = ($effectiveMaxLeadTimeDays > 0) ? now()->addDays($effectiveMaxLeadTimeDays) : null;

        $monthStart = Carbon::create($year, $month, 1)->startOfDay();
        $monthEnd = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $allBusyPeriods = $this->getAllMemberBusyPeriods($members, $monthStart, $monthEnd);
        $allBusyPeriods = $allBusyPeriods->merge(
            $this->getGroupAppointmentBusyPeriods($bookingGroup, $monthStart, $monthEnd, $forMember),
        );

        $now = now()->addHours($effectiveLeadTime);
        $blocks = [];

        /** @var iterable<Carbon> $period */
        $period = CarbonPeriod::create($monthStart, $monthEnd); /** @phpstan-ignore varTag.nativeType */

        foreach ($period as $date) {
            $dayBlocks = $this->getAvailableBlocksForDay(
                $date,
                $groupHours,
                $members,
                $allBusyPeriods,
                $bufferBefore,
                $bufferAfter,
                $now,
                $latestAllowed,
            );

            $blocks = array_merge($blocks, $dayBlocks);
        }

        return $blocks;
    }

    /**
     * @param array<string, mixed> $groupHours
     * @param Collection<int, User> $members
     * @param Collection<int, array{start: Carbon, end: Carbon}> $allBusyPeriods
     *
     * @return array<int, array{start: string, end: string}>
     */
    protected function getAvailableBlocksForDay(
        Carbon $date,
        array $groupHours,
        Collection $members,
        Collection $allBusyPeriods,
        int $bufferBefore,
        int $bufferAfter,
        Carbon $now,
        ?Carbon $latestAllowed = null,
    ): array {
        $dayOfWeek = strtolower($date->format('l'));

        $groupDayHours = $this->getGroupHoursForDay($groupHours, $dayOfWeek);

        if ($groupDayHours === null) {
            return [];
        }

        $groupStart = Carbon::parse("{$date->toDateString()} {$groupDayHours['starts_at']}", 'UTC');
        $groupEnd = Carbon::parse("{$date->toDateString()} {$groupDayHours['ends_at']}", 'UTC');

        if ($groupEnd->lte($now)) {
            return [];
        }

        // Intersect group hours with each member's personal hours
        $intersectedBlocks = [['start' => $groupStart, 'end' => $groupEnd]];

        foreach ($members as $member) {
            if ($this->isOutOfOffice($member, $date)) {
                return [];
            }

            $memberHours = $this->getMemberHoursForDay($member, $dayOfWeek);

            if (empty($memberHours)) {
                return [];
            }

            $memberBlocks = collect($memberHours)->map(function (array $period) use ($date) {
                $startTime = $period['start'] ?? $period['starts_at'];
                $endTime = $period['end'] ?? $period['ends_at'];

                return [
                    'start' => Carbon::parse("{$date->toDateString()} {$startTime}", 'UTC'),
                    'end' => Carbon::parse("{$date->toDateString()} {$endTime}", 'UTC'),
                ];
            })->all();

            $intersectedBlocks = $this->intersectTwoBlockSets($intersectedBlocks, $memberBlocks);

            if (empty($intersectedBlocks)) {
                return [];
            }
        }

        // Carve out busy periods from the intersected blocks
        $availableBlocks = $intersectedBlocks;

        foreach ($allBusyPeriods as $busy) {
            $availableBlocks = $this->splitBlocksAroundBusyPeriod($availableBlocks, $busy);

            if (empty($availableBlocks)) {
                return [];
            }
        }

        // Shrink blocks by buffer so the frontend only offers slots with room for buffer
        if ($bufferBefore > 0 || $bufferAfter > 0) {
            $availableBlocks = collect($availableBlocks)
                ->map(function (array $block) use ($bufferBefore, $bufferAfter) {
                    return [
                        'start' => $block['start']->copy()->addMinutes($bufferBefore),
                        'end' => $block['end']->copy()->subMinutes($bufferAfter),
                    ];
                })
                ->filter(fn (array $block) => $block['start']->lt($block['end']))
                ->values()
                ->all();

            if (empty($availableBlocks)) {
                return [];
            }
        }

        // Filter out past blocks and blocks beyond max lead time, adjust start/end times
        return collect($availableBlocks)
            ->filter(fn (array $block) => $block['end']->isAfter($now))
            ->when(
                $latestAllowed !== null,
                fn (Collection $collection) => $collection
                    ->filter(fn (array $block) => $block['start']->lte($latestAllowed))
            )
            ->map(function (array $block) use ($now, $latestAllowed) {
                $start = $block['start']->isBefore($now) ? $now->copy() : $block['start'];
                $end = ($latestAllowed !== null && $block['end']->isAfter($latestAllowed))
                    ? $latestAllowed->copy()
                    : $block['end'];

                return [
                    'start' => $start->toIso8601String(),
                    'end' => $end->toIso8601String(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $groupHours
     *
     * @return array{starts_at: string, ends_at: string}|null
     */
    protected function getGroupHoursForDay(array $groupHours, string $dayOfWeek): ?array
    {
        $dayConfig = $groupHours[$dayOfWeek] ?? null;

        if (! $dayConfig || ! ($dayConfig['is_enabled'] ?? false)) {
            return null;
        }

        $startsAt = $dayConfig['starts_at'] ?? null;
        $endsAt = $dayConfig['ends_at'] ?? null;

        if (! $startsAt || ! $endsAt) {
            return null;
        }

        return ['starts_at' => $startsAt, 'ends_at' => $endsAt];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getMemberHoursForDay(User $member, string $dayOfWeek): array
    {
        $officeHours = $this->getHoursFromSettings($member->office_hours_are_enabled, $member->office_hours, $dayOfWeek);

        if (! empty($officeHours)) {
            return $officeHours;
        }

        return $this->getHoursFromSettings($member->working_hours_are_enabled, $member->working_hours, $dayOfWeek);
    }

    /**
     * @param array<string, array<string, mixed>>|null $hoursSettings
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getHoursFromSettings(bool $isEnabled, ?array $hoursSettings, string $dayOfWeek): array
    {
        if (! $isEnabled || $hoursSettings === null) {
            return [];
        }

        $dayHours = $hoursSettings[$dayOfWeek] ?? [];

        if (empty($dayHours)) {
            return [];
        }

        if (isset($dayHours['enabled']) || isset($dayHours['is_enabled'])) {
            if (! ($dayHours['enabled'] ?? $dayHours['is_enabled'] ?? false)) {
                return [];
            }

            return [$dayHours];
        }

        return collect($dayHours)
            ->filter(fn (array $period) => ($period['enabled'] ?? $period['is_enabled'] ?? false))
            ->values()
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
     * @param Collection<int, User> $members
     *
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    protected function getAllMemberBusyPeriods(Collection $members, Carbon $start, Carbon $end): Collection
    {
        return $members
            ->flatMap(fn (User $member) => $this->getBusyPeriodsFor($member, $start, $end))
            ->values();
    }

    /**
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    protected function getGroupAppointmentBusyPeriods(BookingGroup $bookingGroup, Carbon $start, Carbon $end, ?User $forMember = null): Collection
    {
        return BookingGroupAppointment::query()
            ->whereBelongsTo($bookingGroup)
            ->when($forMember, fn ($query) => $query->where('meeting_owner_id', $forMember->id))
            ->where('starts_at', '<', $end)
            ->where('ends_at', '>', $start)
            ->get()
            ->map(function (BookingGroupAppointment $appointment) {
                return [
                    'start' => $appointment->starts_at->copy(),
                    'end' => $appointment->ends_at->copy(),
                ];
            })
            ->values();
    }

    /**
     * @return Collection<int, array{start: Carbon, end: Carbon}>
     */
    protected function getBusyPeriodsFor(User $user, Carbon $start, Carbon $end): Collection
    {
        return $this->getCalendarEventsFor($user, $start, $end)
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
            ->where(function (Builder $query) use ($start, $end) {
                $query->whereBetween('starts_at', [$start, $end])
                    ->orWhereBetween('ends_at', [$start, $end])
                    ->orWhere(fn (Builder $subQuery) => $subQuery->where('starts_at', '<=', $start)->where('ends_at', '>=', $end));
            })
            ->where(function (Builder $query) {
                $query->whereIn('transparency', [
                    EventTransparency::Busy->value,
                    EventTransparency::Tentative->value,
                    EventTransparency::OutOfOffice->value,
                    EventTransparency::WorkingElsewhere->value,
                ]);
            })
            ->get();
    }

    /**
     * @param array<int, array{start: Carbon, end: Carbon}> $blocksA
     * @param array<int, array{start: Carbon, end: Carbon}> $blocksB
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    protected function intersectTwoBlockSets(array $blocksA, array $blocksB): array
    {
        $result = [];

        foreach ($blocksA as $blockA) {
            foreach ($blocksB as $blockB) {
                $overlapStart = $blockA['start']->max($blockB['start']);
                $overlapEnd = $blockA['end']->min($blockB['end']);

                if ($overlapStart->lt($overlapEnd)) {
                    $result[] = [
                        'start' => $overlapStart->copy(),
                        'end' => $overlapEnd->copy(),
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param array<int, array{start: Carbon, end: Carbon}> $blocks
     * @param array{start: Carbon, end: Carbon} $busy
     *
     * @return array<int, array{start: Carbon, end: Carbon}>
     */
    protected function splitBlocksAroundBusyPeriod(array $blocks, array $busy): array
    {
        $busyStart = $busy['start'];
        $busyEnd = $busy['end'];

        return collect($blocks)
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

        if ($busyEnd->lte($blockStart) || $busyStart->gte($blockEnd)) {
            return [$block];
        }

        if ($busyStart->lte($blockStart) && $busyEnd->gte($blockEnd)) {
            return [];
        }

        if ($busyStart->gt($blockStart) && $busyEnd->lt($blockEnd)) {
            return [
                ['start' => $blockStart, 'end' => $busyStart->copy()],
                ['start' => $busyEnd->copy(), 'end' => $blockEnd],
            ];
        }

        if ($busyStart->lte($blockStart)) {
            return [['start' => $busyEnd->copy(), 'end' => $blockEnd]];
        }

        return [['start' => $blockStart, 'end' => $busyStart->copy()]];
    }
}
