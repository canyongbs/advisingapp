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

use AdvisingApp\MeetingCenter\Actions\GetAvailableAppointmentSlots;
use AdvisingApp\MeetingCenter\Http\Requests\BookPersonalCalendarSlotRequest;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\PersonalBookingPage;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use App\Http\Controllers\Controller;
use App\Settings\CollegeBrandingSettings;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PersonalBookingPageWidgetController extends Controller
{
    public function assets(Request $request, string $slug): JsonResponse
    {
        PersonalBookingPage::query()
            ->where('slug', $slug)
            ->where('is_enabled', true)
            ->firstOrFail();

        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/widgets/booking-page/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.booking-page.asset'),
            'entry' => route('widgets.booking-page.personal.api.entry', ['slug' => $slug]),
            'js' => route('widgets.booking-page.asset', ['file' => $widgetEntry['file']]),
        ]);
    }

    public function asset(Request $request, ?string $file = null): StreamedResponse
    {
        if (is_null($file)) {
            abort(404, 'File not found.');
        }

        $path = "widgets/booking-page/{$file}";

        $disk = Storage::disk('public');

        abort_if(! $disk->exists($path), 404, 'File not found.');

        $mimeType = $disk->mimeType($path);

        $stream = $disk->readStream($path);

        abort_if(is_null($stream), 404, 'File not found.');

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            $file,
            ['Content-Type' => $mimeType]
        );
    }

    public function view(Request $request, string $slug): JsonResponse
    {
        $bookingPage = PersonalBookingPage::query()
            ->where('slug', $slug)
            ->where('is_enabled', true)
            ->with('user')
            ->firstOrFail();

        $brandingSettings = app(CollegeBrandingSettings::class);

        $colorName = $brandingSettings->color->value ?? 'blue';
        $primaryColor = collect(Color::all()[$colorName])
            ->map(Color::convertToRgb(...))
            ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
            ->all();

        return response()->json([
            'display_name' => $bookingPage->user->name,
            'slug' => $bookingPage->slug,
            'duration' => $bookingPage->default_appointment_duration,
            'timezone' => $bookingPage->user->timezone ?? config('app.timezone'),
            'primary_color' => $primaryColor,
            'booking_url' => route('widgets.booking-page.personal.api.book', ['slug' => $slug]),
            'available_slots_url' => route('widgets.booking-page.personal.api.available-slots', ['slug' => $slug]),
        ]);
    }

    public function availableSlots(Request $request, string $slug, GetAvailableAppointmentSlots $getAvailableSlots): JsonResponse
    {
        $bookingPage = PersonalBookingPage::query()
            ->where('slug', $slug)
            ->where('is_enabled', true)
            ->with('user')
            ->firstOrFail();

        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        $blocks = $getAvailableSlots(
            $bookingPage->user,
            $year,
            $month
        );

        return response()->json([
            'blocks' => $blocks,
        ]);
    }

    public function book(BookPersonalCalendarSlotRequest $request, string $slug): JsonResponse
    {
        $bookingPage = PersonalBookingPage::query()
            ->where('slug', $slug)
            ->where('is_enabled', true)
            ->with(['user', 'user.calendar'])
            ->firstOrFail();

        $user = $bookingPage->user;

        if (! $user->calendar) {
            return response()->json([
                'success' => false,
                'message' => 'Calendar is not configured for this user.',
            ], 422);
        }

        // Check if appointments are restricted to existing students
        if ($user->appointments_are_restricted_to_existing_students) {
            $emailExists = StudentEmailAddress::query()
                ->where('address', $request->validated('email'))
                ->exists();

            if (! $emailExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointments are restricted to existing students. The email address provided is not associated with a student account.',
                ], 403);
            }
        }

        $startsAt = Carbon::parse($request->validated('starts_at'));
        $endsAt = Carbon::parse($request->validated('ends_at'));

        // Check if the appointment has already started
        if ($startsAt->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot book appointments that have already started. Please select a future time slot.',
            ], 422);
        }

        // Check if slot is still available using a database lock
        return DB::transaction(function () use ($user, $startsAt, $endsAt, $request) {
            // Check for overlapping events
            $hasConflict = CalendarEvent::query()
                ->where('calendar_id', $user->calendar->id)
                ->where('starts_at', '<', $endsAt)
                ->where('ends_at', '>', $startsAt)
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'This time slot has already been booked. Please select another time.',
                ], 409);
            }

            // Create the calendar event
            $event = CalendarEvent::create([
                'calendar_id' => $user->calendar->id,
                'title' => 'Meeting with ' . $request->validated('name'),
                'description' => 'Booked via personal booking page',
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'attendees' => [
                    $request->validated('email'),
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your appointment has been successfully booked!',
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'starts_at' => $event->starts_at->toIso8601String(),
                    'ends_at' => $event->ends_at->toIso8601String(),
                ],
            ], 201);
        });
    }
}
