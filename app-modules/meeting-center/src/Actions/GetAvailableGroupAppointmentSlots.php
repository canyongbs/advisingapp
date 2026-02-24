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

namespace AdvisingApp\MeetingCenter\Actions;

use AdvisingApp\MeetingCenter\Models\BookingGroup;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class GetAvailableGroupAppointmentSlots
{
    protected GetAvailableAppointmentSlots $personalSlots;

    public function __construct()
    {
        $this->personalSlots = new GetAvailableAppointmentSlots();
    }

    /**
     * @return array<int, array{start: string, end: string}>
     */
    public function __invoke(BookingGroup $bookingGroup, int $year, int $month): array
    {
        $members = $bookingGroup->allMembers();

        if ($members->isEmpty()) {
            return [];
        }

        $period = CarbonPeriod::create(
            Carbon::create($year, $month, 1)->startOfDay(),
            Carbon::create($year, $month, 1)->endOfMonth()->endOfDay()
        );

        assert($period->start instanceof Carbon);
        assert($period->end instanceof Carbon);

        // Get available blocks for each member individually
        $memberBlocks = $members->map(function (User $user) use ($year, $month): array {
            return ($this->personalSlots)($user, $year, $month);
        });

        // Find the intersection: only times where ALL members are available
        return $this->intersectBlocks($memberBlocks, $bookingGroup, $year, $month);
    }

    /**
     * @param Collection<int, array<int, array{start: string, end: string}>> $memberBlocks
     *
     * @return array<int, array{start: string, end: string}>
     */
    protected function intersectBlocks(Collection $memberBlocks, BookingGroup $bookingGroup, int $year, int $month): array
    {
        if ($memberBlocks->isEmpty()) {
            return [];
        }

        // Start with the group's configured hours as the base
        $groupBlocks = $this->getGroupScheduleBlocks($bookingGroup, $year, $month);

        if (empty($groupBlocks)) {
            return [];
        }

        // Intersect with each member's available blocks
        $intersected = $groupBlocks;

        foreach ($memberBlocks as $blocks) {
            $intersected = $this->intersectTwoBlockSets($intersected, $blocks);

            if (empty($intersected)) {
                return [];
            }
        }

        return $intersected;
    }

    /**
     * @return array<int, array{start: string, end: string}>
     */
    protected function getGroupScheduleBlocks(BookingGroup $bookingGroup, int $year, int $month): array
    {
        $hours = $bookingGroup->available_appointment_hours;

        if (empty($hours)) {
            return [];
        }

        $now = now();
        $blocks = [];

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $period = CarbonPeriod::create($start, $end);

        foreach ($period as $date) {
            $dayOfWeek = strtolower($date->format('l'));
            $dayConfig = $hours[$dayOfWeek] ?? null;

            if (! $dayConfig || ! ($dayConfig['is_enabled'] ?? false)) {
                continue;
            }

            $startsAt = $dayConfig['starts_at'] ?? null;
            $endsAt = $dayConfig['ends_at'] ?? null;

            if (! $startsAt || ! $endsAt) {
                continue;
            }

            $blockStart = Carbon::parse("{$date->toDateString()} {$startsAt}", 'UTC');
            $blockEnd = Carbon::parse("{$date->toDateString()} {$endsAt}", 'UTC');

            if ($blockEnd->isAfter($now)) {
                $effectiveStart = $blockStart->isBefore($now) ? $now->copy() : $blockStart;

                $blocks[] = [
                    'start' => $effectiveStart->toIso8601String(),
                    'end' => $blockEnd->toIso8601String(),
                ];
            }
        }

        return $blocks;
    }

    /**
     * @param array<int, array{start: string, end: string}> $blocksA
     * @param array<int, array{start: string, end: string}> $blocksB
     *
     * @return array<int, array{start: string, end: string}>
     */
    protected function intersectTwoBlockSets(array $blocksA, array $blocksB): array
    {
        $result = [];

        foreach ($blocksA as $a) {
            $aStart = Carbon::parse($a['start']);
            $aEnd = Carbon::parse($a['end']);

            foreach ($blocksB as $b) {
                $bStart = Carbon::parse($b['start']);
                $bEnd = Carbon::parse($b['end']);

                // Find the overlap
                $overlapStart = $aStart->max($bStart);
                $overlapEnd = $aEnd->min($bEnd);

                if ($overlapStart->lt($overlapEnd)) {
                    $result[] = [
                        'start' => $overlapStart->toIso8601String(),
                        'end' => $overlapEnd->toIso8601String(),
                    ];
                }
            }
        }

        return $result;
    }
}
