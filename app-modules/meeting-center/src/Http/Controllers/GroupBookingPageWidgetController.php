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

namespace AdvisingApp\MeetingCenter\Http\Controllers;

use AdvisingApp\MeetingCenter\Enums\BookingGroupBookWith;
use AdvisingApp\MeetingCenter\Http\Requests\BookGroupCalendarSlotRequest;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use App\Features\BookingGroupRoundRobinFeature;
use App\Http\Controllers\Controller;
use App\Settings\CollegeBrandingSettings;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GroupBookingPageWidgetController extends Controller
{
    public function assets(Request $request, string $slug): JsonResponse
    {
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
            'book_with' => $bookingGroup->book_with->value,
            'booking_url' => route('widgets.booking-page.group.api.book', ['slug' => $slug]),
            'available_slots_url' => route('widgets.booking-page.group.api.available-slots', ['slug' => $slug]),
        ]);
    }

    public function availableSlots(Request $request, string $slug): JsonResponse
    {
        $bookingGroup = BookingGroup::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $year = $request->integer('year', now()->year);
        $month = $request->integer('month', now()->month);

        if (in_array($bookingGroup->book_with, [BookingGroupBookWith::RoundRobin, BookingGroupBookWith::Availability]) && ! BookingGroupRoundRobinFeature::active()) {
            return response()->json([
                'blocks' => [],
            ]);
        }

        return $bookingGroup->book_with->getBooker()->availableSlots($bookingGroup, $year, $month);
    }

    public function book(BookGroupCalendarSlotRequest $request, string $slug): JsonResponse
    {
        $bookingGroup = BookingGroup::query()
            ->where('slug', $slug)
            ->firstOrFail();

        if (in_array($bookingGroup->book_with, [BookingGroupBookWith::RoundRobin, BookingGroupBookWith::Availability]) && ! BookingGroupRoundRobinFeature::active()) {
            return response()->json([
                'success' => false,
                'message' => 'This booking type is not currently available.',
            ], 422);
        }

        return $bookingGroup->book_with->getBooker()->book($request, $bookingGroup);
    }
}
