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

namespace AdvisingApp\MeetingCenter\Http\Controllers;

use AdvisingApp\MeetingCenter\Actions\GetAvailableGroupAppointmentSlots;
use AdvisingApp\MeetingCenter\Http\Requests\BookGroupCalendarSlotRequest;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use App\Features\GroupBookingFeature;
use App\Http\Controllers\Controller;
use App\Settings\CollegeBrandingSettings;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GroupBookingPageWidgetController extends Controller
{
    public function assets(Request $request, string $slug): JsonResponse
    {
        abort_unless(GroupBookingFeature::active(), 404);

        BookingGroup::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $manifestPath = public_path('storage/widgets/booking-page/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.booking-page.asset'),
            'entry' => route('widgets.booking-page.group.api.entry', ['slug' => $slug]),
            'js' => route('widgets.booking-page.asset', ['file' => $widgetEntry['file']]),
        ]);
    }

    public function view(Request $request, string $slug): JsonResponse
    {
        abort_unless(GroupBookingFeature::active(), 404);

        $bookingGroup = BookingGroup::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $brandingSettings = app(CollegeBrandingSettings::class);

        $colorName = $brandingSettings->color->value ?? 'blue';
        $primaryColor = collect(Color::all()[$colorName])
            ->map(Color::convertToRgb(...))
            ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
            ->all();

        return response()->json([
            'display_name' => $bookingGroup->name,
            'slug' => $bookingGroup->slug,
            'duration' => $bookingGroup->default_appointment_duration,
            'timezone' => config('app.timezone'),
            'primary_color' => $primaryColor,
            'booking_url' => route('widgets.booking-page.group.api.book', ['slug' => $slug]),
            'available_slots_url' => route('widgets.booking-page.group.api.available-slots', ['slug' => $slug]),
        ]);
    }

    public function availableSlots(Request $request, string $slug, GetAvailableGroupAppointmentSlots $getAvailableSlots): JsonResponse
    {
        abort_unless(GroupBookingFeature::active(), 404);

        $bookingGroup = BookingGroup::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $blocks = $getAvailableSlots(
            $bookingGroup,
            $year,
            $month
        );

        return response()->json([
            'blocks' => $blocks,
        ]);
    }

    public function book(BookGroupCalendarSlotRequest $request, string $slug): JsonResponse
    {
        abort_unless(GroupBookingFeature::active(), 404);

        $bookingGroup = BookingGroup::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $members = $bookingGroup->allMembers();

        $startsAt = Carbon::parse($request->validated('starts_at'));
        $endsAt = Carbon::parse($request->validated('ends_at'));

        if ($startsAt->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book appointments that have already started. Please select a future time slot.',
            ], 422);
        }

        $maxBuffer = $bookingGroup->is_default_appointment_buffer_enabled
            ? max($bookingGroup->default_appointment_buffer_before_duration, $bookingGroup->default_appointment_buffer_after_duration)
            : 0;

        $conflictCheckStart = $startsAt->copy()->subMinutes($maxBuffer);
        $conflictCheckEnd = $endsAt->copy()->addMinutes($maxBuffer);

        $bufferBefore = $bookingGroup->is_default_appointment_buffer_enabled
            ? $bookingGroup->default_appointment_buffer_before_duration
            : 0;

        $bufferAfter = $bookingGroup->is_default_appointment_buffer_enabled
            ? $bookingGroup->default_appointment_buffer_after_duration
            : 0;

        $calendarStartsAt = $startsAt->copy()->subMinutes($bufferBefore);
        $calendarEndsAt = $endsAt->copy()->addMinutes($bufferAfter);

        // Validate the requested slot fits within regenerated available blocks
        $availableBlocks = app(GetAvailableGroupAppointmentSlots::class)(
            $bookingGroup,
            $startsAt->year,
            $startsAt->month,
        );

        $slotIsValid = collect($availableBlocks)->contains(function (array $block) use ($startsAt, $endsAt) {
            $blockStart = Carbon::parse($block['start']);
            $blockEnd = Carbon::parse($block['end']);

            return $startsAt->gte($blockStart) && $endsAt->lte($blockEnd);
        });

        if (! $slotIsValid) {
            return response()->json([
                'success' => false,
                'message' => 'This time slot is no longer available. Please select another time.',
            ], 409);
        }

        return DB::transaction(function () use ($bookingGroup, $members, $startsAt, $endsAt, $calendarStartsAt, $calendarEndsAt, $conflictCheckStart, $conflictCheckEnd, $bufferBefore, $bufferAfter, $request) {
            // Check for overlapping events across ALL members' calendars, accounting for buffer time
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
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot has already been booked. Please select another time.',
                    ], 409);
                }
            }

            // Create calendar events on each member's connected calendar with buffer included
            foreach ($members as $member) {
                if (! $member->calendar) {
                    continue;
                }

                $description = 'Booked via group booking page: ' . $bookingGroup->name;

                if ($bufferBefore > 0 || $bufferAfter > 0) {
                    $description .= "\n\nAppointment: " . $startsAt->format('g:i A') . ' - ' . $endsAt->format('g:i A');

                    if ($bufferBefore > 0) {
                        $description .= "\nBuffer before: " . CarbonInterval::minutes($bufferBefore)->cascade()->forHumans(['short' => true]);
                    }

                    if ($bufferAfter > 0) {
                        $description .= "\nBuffer after: " . CarbonInterval::minutes($bufferAfter)->cascade()->forHumans(['short' => true]);
                    }
                }

                CalendarEvent::create([
                    'calendar_id' => $member->calendar->id,
                    'title' => 'Group Meeting with ' . $request->validated('name'),
                    'description' => $description,
                    'starts_at' => $calendarStartsAt,
                    'ends_at' => $calendarEndsAt,
                    'attendees' => [
                        $request->validated('email'),
                    ],
                ]);
            }

            $appointment = BookingGroupAppointment::create([
                'booking_group_id' => $bookingGroup->id,
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your appointment has been successfully booked!',
                'event' => [
                    'id' => $appointment->id,
                    'title' => 'Group Meeting with ' . $request->validated('name'),
                    'starts_at' => $startsAt->toIso8601String(),
                    'ends_at' => $endsAt->toIso8601String(),
                ],
            ], 201);
        });
    }
}
