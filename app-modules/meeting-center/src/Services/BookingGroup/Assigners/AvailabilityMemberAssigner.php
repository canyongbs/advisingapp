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

namespace AdvisingApp\MeetingCenter\Services\BookingGroup\Assigners;

use AdvisingApp\MeetingCenter\Enums\EventTransparency;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Services\BookingGroup\BookableWindowResolver;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class AvailabilityMemberAssigner implements BookingGroupMemberAssigner
{
    public function resolve(BookingGroup $bookingGroup): ?User
    {
        $memberIds = $bookingGroup->allMembers()->pluck('id');

        if ($memberIds->isEmpty()) {
            return null;
        }

        $eligibleMembers = User::query()
            ->whereIn('users.id', $memberIds)
            ->whereHas('calendar', fn (Builder $query) => $query->whereNotNull('provider_id'))
            ->get();

        if ($eligibleMembers->isEmpty()) {
            return null;
        }

        [$windowStart, $windowEnd] = BookableWindowResolver::resolve($bookingGroup);

        $memberHours = $this->calculateMemberMeetingHours($eligibleMembers, $windowStart, $windowEnd);

        $minHours = (float) $memberHours->min();

        $tiedMembers = $memberHours->filter(fn (float $hours) => $hours === $minHours)->keys();

        if ($tiedMembers->count() === 1) {
            return $eligibleMembers->firstWhere('id', $tiedMembers->first());
        }

        return $this->resolveRoundRobinTiebreaker($bookingGroup, $eligibleMembers, $tiedMembers);
    }

    public function advance(BookingGroup $bookingGroup, User $member): void
    {
        $bookingGroup->round_robin_last_assigned_id = $member->getKey();
        $bookingGroup->save();
    }

    /**
     * @param  Collection<int, User>  $members
     *
     * @return Collection<string, float>
     */
    protected function calculateMemberMeetingHours(Collection $members, Carbon $windowStart, Carbon $windowEnd): Collection
    {
        $memberHours = collect();

        foreach ($members as $member) {
            if (! $member->calendar) {
                $memberHours->put($member->id, 0.0);

                continue;
            }

            $totalSeconds = CalendarEvent::query()
                ->where('calendar_id', $member->calendar->id)
                ->where('starts_at', '<', $windowEnd)
                ->where('ends_at', '>', $windowStart)
                ->whereIn('transparency', [
                    EventTransparency::Busy->value,
                    EventTransparency::Tentative->value,
                    EventTransparency::OutOfOffice->value,
                    EventTransparency::WorkingElsewhere->value,
                ])
                ->get()
                ->sum(function (CalendarEvent $event) use ($windowStart, $windowEnd) {
                    $start = $event->starts_at->max($windowStart);
                    $end = $event->ends_at->min($windowEnd);

                    return max(0, $start->diffInSeconds($end));
                });

            $memberHours->put($member->id, (float) ($totalSeconds / 3600));
        }

        return $memberHours;
    }

    /**
     * @param  Collection<int, User>  $eligibleMembers
     * @param  Collection<int, string>  $tiedMemberIds
     */
    protected function resolveRoundRobinTiebreaker(BookingGroup $bookingGroup, Collection $eligibleMembers, Collection $tiedMemberIds): ?User
    {
        $lastAssignee = $bookingGroup->roundRobinLastAssignedUser;

        if ($lastAssignee && $tiedMemberIds->contains($lastAssignee->id)) {
            $user = User::query()
                ->whereIn('users.id', $tiedMemberIds)
                ->whereHas('calendar', fn (Builder $query) => $query->whereNotNull('provider_id'))
                ->where('name', '>=', $lastAssignee->name)
                ->where(fn (Builder $query) => $query
                    ->where('name', '!=', $lastAssignee->name)
                    ->orWhere('users.id', '>', $lastAssignee->getKey()))
                ->orderBy('name')
                ->orderBy('users.id')
                ->first();

            if ($user) {
                return $user;
            }
        }

        return User::query()
            ->whereIn('users.id', $tiedMemberIds)
            ->whereHas('calendar', fn (Builder $query) => $query->whereNotNull('provider_id'))
            ->orderBy('name')
            ->orderBy('users.id')
            ->first();
    }
}
