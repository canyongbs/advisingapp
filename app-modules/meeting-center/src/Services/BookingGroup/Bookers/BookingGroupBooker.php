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

use AdvisingApp\MeetingCenter\Enums\EventTransparency;
use AdvisingApp\MeetingCenter\Http\Requests\BookGroupCalendarSlotRequest;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

abstract class BookingGroupBooker
{
    abstract public function availableSlots(BookingGroup $bookingGroup, int $year, int $month): JsonResponse;

    abstract public function book(BookGroupCalendarSlotRequest $request, BookingGroup $bookingGroup): JsonResponse;

    /**
     * @param  Collection<int, User>  $members
     */
    protected function validateLeadTime(BookingGroup $bookingGroup, Collection $members, Carbon $startsAt): ?JsonResponse
    {
        $memberMaxLeadTime = PersonalBookingPage::query()
            ->whereIn('user_id', $members->pluck('id'))
            ->max('minimum_booking_lead_time_hours') ?? 0;

        $effectiveLeadTime = max($bookingGroup->minimum_booking_lead_time_hours ?? 0, $memberMaxLeadTime);
        $earliestAllowed = now()->addHours($effectiveLeadTime);

        if ($startsAt->isBefore($earliestAllowed)) {
            return response()->json([
                'success' => false,
                'message' => $effectiveLeadTime > 0
                    ? "Bookings require at least {$effectiveLeadTime} hours advance notice. Please select a later time slot."
                    : 'Cannot book appointments that have already started. Please select a future time slot.',
            ], 422);
        }

        return null;
    }

    /**
     * @param  Collection<int, User>  $members
     */
    protected function validateMaxLeadTime(BookingGroup $bookingGroup, Collection $members, Carbon $startsAt): ?JsonResponse
    {
        $memberMaxLeadTimeDays = PersonalBookingPage::query()
            ->whereIn('user_id', $members->pluck('id'))
            ->max('maximum_booking_lead_time_days') ?? 0;
        $effectiveMaxLeadTimeDays = max($bookingGroup->maximum_booking_lead_time_days ?? 0, $memberMaxLeadTimeDays);

        if ($effectiveMaxLeadTimeDays > 0) {
            $latestAllowed = now()->addDays($effectiveMaxLeadTimeDays);

            if ($startsAt->isAfter($latestAllowed)) {
                return response()->json([
                    'success' => false,
                    'message' => "Bookings cannot be made more than {$effectiveMaxLeadTimeDays} days in advance. Please select an earlier time slot.",
                ], 422);
            }
        }

        return null;
    }

    /**
     * @return array{int, int}
     */
    protected function resolveBufferTimes(BookingGroup $bookingGroup): array
    {
        $bufferBefore = $bookingGroup->is_default_appointment_buffer_enabled
            ? $bookingGroup->default_appointment_buffer_before_duration
            : 0;

        $bufferAfter = $bookingGroup->is_default_appointment_buffer_enabled
            ? $bookingGroup->default_appointment_buffer_after_duration
            : 0;

        return [$bufferBefore, $bufferAfter];
    }

    /**
     * @param array<int, string> $attendees
     *
     * @return array{CalendarEvent, BookingGroupAppointment}
     */
    protected function createCalendarEventAndAppointment(
        BookingGroup $bookingGroup,
        User $calendarOwner,
        array $attendees,
        Carbon $startsAt,
        Carbon $endsAt,
        string $name,
        string $email,
        ?string $meetingOwnerId = null,
    ): array {
        $description = 'Booked via group booking page: ' . $bookingGroup->name;

        $calendarEvent = CalendarEvent::create([
            'calendar_id' => $calendarOwner->calendar->id,
            'title' => 'Group Meeting with ' . $name,
            'description' => $description,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'attendees' => $attendees,
            'transparency' => EventTransparency::Busy,
        ]);

        if ($calendarEvent->provider_uid === null) {
            report(new Exception('Calendar event was created but provider UID was not returned.'));
        }

        $appointment = BookingGroupAppointment::create([
            'booking_group_id' => $bookingGroup->id,
            'calendar_event_provider_uid' => $calendarEvent->provider_uid,
            'name' => $name,
            'email' => $email,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'meeting_owner_id' => $meetingOwnerId,
        ]);

        return [$calendarEvent, $appointment];
    }

    protected function successResponse(BookingGroupAppointment $appointment, string $name, Carbon $startsAt, Carbon $endsAt): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Your appointment has been successfully booked!',
            'event' => [
                'id' => $appointment->id,
                'title' => 'Group Meeting with ' . $name,
                'starts_at' => $startsAt->toIso8601String(),
                'ends_at' => $endsAt->toIso8601String(),
            ],
        ], 201);
    }
}
