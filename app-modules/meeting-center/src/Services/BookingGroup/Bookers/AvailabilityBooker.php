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

use AdvisingApp\MeetingCenter\Http\Requests\BookGroupCalendarSlotRequest;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AvailabilityBooker extends RoundRobinBooker
{
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

            $assigner = $bookingGroup->book_with->getAssignerClass();
            $member = $assigner->resolve($bookingGroup);

            if (! $member) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available members with connected calendars.',
                ], 422);
            }

            if ($this->hasMemberConflict($bookingGroup, $member, $conflictCheckStart, $conflictCheckEnd)) {
                $bookingGroup->unsetRelation('roundRobinLastAssignedUser');
                $newMember = $assigner->resolve($bookingGroup);

                if ($newMember && $newMember->id !== $member->id && ! $this->hasMemberConflict($bookingGroup, $newMember, $conflictCheckStart, $conflictCheckEnd)) {
                    $member = $newMember;
                } else {
                    return $this->conflictResponseWithFreshBlocks($bookingGroup, $startsAt);
                }
            }

            $attendees = collect([$member->email, $request->validated('email')])
                ->filter()
                ->unique()
                ->values()
                ->all();

            [, $appointment] = $this->createCalendarEventAndAppointment(
                $bookingGroup,
                $member,
                $attendees,
                $startsAt,
                $endsAt,
                $request->validated('name'),
                $request->validated('email'),
                $member->id,
            );

            $assigner->advance($bookingGroup, $member);

            return $this->successResponse($appointment, $request->validated('name'), $startsAt, $endsAt);
        });
    }
}
