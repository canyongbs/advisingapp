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
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AllBooker extends BookingGroupBooker
{
    public function availableSlots(BookingGroup $bookingGroup, int $year, int $month): JsonResponse
    {
        $blocks = app(GetAvailableGroupAppointmentSlots::class)(
            $bookingGroup,
            $year,
            $month,
        );

        return response()->json([
            'blocks' => $blocks,
        ]);
    }

    public function book(BookGroupCalendarSlotRequest $request, BookingGroup $bookingGroup): JsonResponse
    {
        $members = $bookingGroup->allMembers();
        $meetingOwner = $bookingGroup->meetingOwner;

        if (! $meetingOwner || ! $members->contains('id', $meetingOwner->id)) {
            return response()->json([
                'success' => false,
                'message' => 'This booking group does not have a valid meeting owner configured.',
            ], 422);
        }

        if (! $meetingOwner->calendar?->provider_id) {
            return response()->json([
                'success' => false,
                'message' => 'This booking group does not have a valid meeting owner calendar configured.',
            ], 422);
        }

        $startsAt = Carbon::parse($request->validated('starts_at'));
        $endsAt = Carbon::parse($request->validated('ends_at'));

        $leadTimeError = $this->validateLeadTime($bookingGroup, $members, $startsAt);

        if ($leadTimeError) {
            return $leadTimeError;
        }

        $maxLeadTimeError = $this->validateMaxLeadTime($bookingGroup, $members, $startsAt);

        if ($maxLeadTimeError) {
            return $maxLeadTimeError;
        }

        if (! $this->slotIsAvailable($bookingGroup, $startsAt, $endsAt)) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is no longer available. Please select another time.',
            ], 409);
        }

        [$bufferBefore, $bufferAfter] = $this->resolveBufferTimes($bookingGroup);
        $conflictCheckStart = $startsAt->copy()->subMinutes($bufferBefore);
        $conflictCheckEnd = $endsAt->copy()->addMinutes($bufferAfter);

        return DB::transaction(function () use ($bookingGroup, $members, $meetingOwner, $startsAt, $endsAt, $conflictCheckStart, $conflictCheckEnd, $request) {
            if ($this->hasCalendarConflicts($members, $conflictCheckStart, $conflictCheckEnd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot has already been booked. Please select another time.',
                ], 409);
            }

            if ($this->hasGroupAppointmentConflict($bookingGroup, $conflictCheckStart, $conflictCheckEnd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot has already been booked. Please select another time.',
                ], 409);
            }

            $attendees = $members
                ->pluck('email')
                ->push($request->validated('email'))
                ->filter()
                ->unique()
                ->values()
                ->all();

            [, $appointment] = $this->createCalendarEventAndAppointment(
                $bookingGroup,
                $meetingOwner,
                $attendees,
                $startsAt,
                $endsAt,
                $request->validated('name'),
                $request->validated('email'),
            );

            return $this->successResponse($appointment, $request->validated('name'), $startsAt, $endsAt);
        });
    }

    protected function slotIsAvailable(BookingGroup $bookingGroup, Carbon $startsAt, Carbon $endsAt): bool
    {
        $availableBlocks = app(GetAvailableGroupAppointmentSlots::class)(
            $bookingGroup,
            $startsAt->year,
            $startsAt->month,
        );

        return collect($availableBlocks)->contains(function (array $block) use ($startsAt, $endsAt) {
            $blockStart = Carbon::parse($block['start']);
            $blockEnd = Carbon::parse($block['end']);

            return $startsAt->gte($blockStart) && $endsAt->lte($blockEnd);
        });
    }

    /**
     * @param  Collection<int, User>  $members
     */
    protected function hasCalendarConflicts(Collection $members, Carbon $conflictCheckStart, Carbon $conflictCheckEnd): bool
    {
        foreach ($members as $member) {
            if (! $member->calendar) {
                continue;
            }

            $hasConflict = CalendarEvent::query()
                ->where('calendar_id', $member->calendar->id)
                ->where('starts_at', '<', $conflictCheckEnd)
                ->where('ends_at', '>', $conflictCheckStart)
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                return true;
            }
        }

        return false;
    }

    protected function hasGroupAppointmentConflict(BookingGroup $bookingGroup, Carbon $conflictCheckStart, Carbon $conflictCheckEnd): bool
    {
        return BookingGroupAppointment::query()
            ->whereBelongsTo($bookingGroup)
            ->where('starts_at', '<', $conflictCheckEnd)
            ->where('ends_at', '>', $conflictCheckStart)
            ->lockForUpdate()
            ->exists();
    }
}
