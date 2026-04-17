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

namespace AdvisingApp\MeetingCenter\Services\BookingGroup\Bookers;

use AdvisingApp\MeetingCenter\Actions\GetAvailableGroupAppointmentSlots;
use AdvisingApp\MeetingCenter\Http\Requests\BookGroupCalendarSlotRequest;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Features\BookingGroupRoundRobinFeature;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class RoundRobinBooker extends BookingGroupBooker
{
    public function availableSlots(BookingGroup $bookingGroup, int $year, int $month): JsonResponse
    {
        $assigner = $bookingGroup->book_with->getAssigner();
        $roundRobinMember = $assigner->resolve($bookingGroup);

        if (! $roundRobinMember) {
            return response()->json([
                'blocks' => [],
            ]);
        }

        $blocks = app(GetAvailableGroupAppointmentSlots::class)(
            $bookingGroup,
            $year,
            $month,
            $roundRobinMember,
        );

        return response()->json([
            'blocks' => $blocks,
        ]);
    }

    public function book(BookGroupCalendarSlotRequest $request, BookingGroup $bookingGroup): JsonResponse
    {
        $startsAt = Carbon::parse($request->validated('starts_at'));
        $endsAt = Carbon::parse($request->validated('ends_at'));

        $members = $bookingGroup->allMembers();

        $leadTimeError = $this->validateLeadTime($bookingGroup, $members, $startsAt);

        if ($leadTimeError) {
            return $leadTimeError;
        }

        $maxLeadTimeError = $this->validateMaxLeadTime($bookingGroup, $members, $startsAt);

        if ($maxLeadTimeError) {
            return $maxLeadTimeError;
        }

        [$bufferBefore, $bufferAfter] = $this->resolveBufferTimes($bookingGroup);
        $conflictCheckStart = $startsAt->copy()->subMinutes($bufferBefore);
        $conflictCheckEnd = $endsAt->copy()->addMinutes($bufferAfter);

        return DB::transaction(function () use ($bookingGroup, $startsAt, $endsAt, $conflictCheckStart, $conflictCheckEnd, $request) {
            BookingGroup::query()->where('id', $bookingGroup->id)->lockForUpdate()->first();
            $bookingGroup->refresh();
            $bookingGroup->unsetRelations();

            $assigner = $bookingGroup->book_with->getAssigner();
            $roundRobinMember = $assigner->resolve($bookingGroup);

            if (! $roundRobinMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available members with connected calendars.',
                ], 422);
            }

            if ($this->hasMemberConflict($bookingGroup, $roundRobinMember, $conflictCheckStart, $conflictCheckEnd)) {
                return $this->conflictResponseWithFreshBlocks($bookingGroup, $startsAt);
            }

            $attendees = collect([$roundRobinMember->email, $request->validated('email')])
                ->filter()
                ->unique()
                ->values()
                ->all();

            [, $appointment] = $this->createCalendarEventAndAppointment(
                $bookingGroup,
                $roundRobinMember,
                $attendees,
                $startsAt,
                $endsAt,
                $request->validated('name'),
                $request->validated('email'),
                $roundRobinMember->id,
            );

            $assigner->advance($bookingGroup, $roundRobinMember);

            return $this->successResponse($appointment, $request->validated('name'), $startsAt, $endsAt);
        });
    }

    protected function hasMemberConflict(BookingGroup $bookingGroup, User $member, Carbon $conflictCheckStart, Carbon $conflictCheckEnd): bool
    {
        $hasCalendarConflict = CalendarEvent::query()
            ->where('calendar_id', $member->calendar->id)
            ->where('starts_at', '<', $conflictCheckEnd)
            ->where('ends_at', '>', $conflictCheckStart)
            ->lockForUpdate()
            ->exists();

        if ($hasCalendarConflict) {
            return true;
        }

        return BookingGroupAppointment::query()
            ->whereBelongsTo($bookingGroup)
            ->when(BookingGroupRoundRobinFeature::active(), fn ($query) => $query->where('meeting_owner_id', $member->id))
            ->where('starts_at', '<', $conflictCheckEnd)
            ->where('ends_at', '>', $conflictCheckStart)
            ->lockForUpdate()
            ->exists();
    }

    protected function conflictResponseWithFreshBlocks(BookingGroup $bookingGroup, Carbon $startsAt): JsonResponse
    {
        $bookingGroup->unsetRelation('roundRobinLastAssignedUser');
        $assigner = $bookingGroup->book_with->getAssigner();
        $newMember = $assigner->resolve($bookingGroup);

        $freshBlocks = [];

        if ($newMember) {
            $freshBlocks = app(GetAvailableGroupAppointmentSlots::class)(
                $bookingGroup,
                $startsAt->year,
                $startsAt->month,
                $newMember,
            );
        }

        return response()->json([
            'success' => false,
            'message' => 'This time slot is no longer available.',
            'blocks' => $freshBlocks,
        ], 409);
    }
}
