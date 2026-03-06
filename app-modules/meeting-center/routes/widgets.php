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

use AdvisingApp\MeetingCenter\Http\Controllers\EventRegistrationWidgetController;
use AdvisingApp\MeetingCenter\Http\Controllers\GroupBookingPageWidgetController;
use AdvisingApp\MeetingCenter\Http\Controllers\PersonalBookingPageWidgetController;
use AdvisingApp\MeetingCenter\Http\Middleware\EnsureEventRegistrationFormIsEmbeddableAndAuthorized;
use AdvisingApp\MeetingCenter\Http\Middleware\EventRegistrationWidgetCors;
use AdvisingApp\MeetingCenter\Models\Event;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'api',
    EncryptCookies::class,
    EventRegistrationWidgetCors::class,
])
    ->prefix('widgets/event-registration')
    ->name('widgets.event-registration.')
    ->group(function () {
        Route::prefix('api/{event}')
            ->name('api.')
            ->middleware([
                EnsureEventRegistrationFormIsEmbeddableAndAuthorized::class . ':event',
            ])
            ->group(function () {
                Route::get('/', [EventRegistrationWidgetController::class, 'assets'])
                    ->name('assets');

                Route::get('entry', [EventRegistrationWidgetController::class, 'view'])
                    ->name('entry');
                Route::post('authenticate/request', [EventRegistrationWidgetController::class, 'requestAuthentication'])
                    ->middleware(['signed'])
                    ->name('request-authentication');
                Route::post('authenticate/{authentication}', [EventRegistrationWidgetController::class, 'authenticate'])
                    ->middleware(['signed'])
                    ->name('authenticate');
                Route::post('submit', [EventRegistrationWidgetController::class, 'store'])
                    ->middleware(['signed'])
                    ->name('submit');

                // Handle preflight CORS requests for all routes in this group
                // MUST remain the last route in this group
                Route::options('/{any}', function (Request $request, Event $event) {
                    return response()->noContent();
                })
                    ->where('any', '.*')
                    ->name('preflight');
            });

        // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
        // NGINX has been configured to route all requests for assets under /widgets to the application
        Route::get('{file?}', [EventRegistrationWidgetController::class, 'asset'])
            ->where('file', '(.*)')
            ->name('asset');
    });

Route::middleware([
    'api',
    EncryptCookies::class,
])
    ->prefix('widgets/booking-page')
    ->name('widgets.booking-page.')
    ->group(function () {
        // Personal booking API routes
        Route::prefix('personal/api/{slug}')
            ->name('personal.api.')
            ->group(function () {
                Route::get('/', [PersonalBookingPageWidgetController::class, 'assets'])
                    ->name('assets');

                Route::get('entry', [PersonalBookingPageWidgetController::class, 'view'])
                    ->name('entry');

                Route::get('available-slots', [PersonalBookingPageWidgetController::class, 'availableSlots'])
                    ->name('available-slots');

                Route::post('book', [PersonalBookingPageWidgetController::class, 'book'])
                    ->name('book');
            });

        // Group booking API routes
        Route::prefix('group/api/{slug}')
            ->name('group.api.')
            ->group(function () {
                Route::get('/', [GroupBookingPageWidgetController::class, 'assets'])
                    ->name('assets');

                Route::get('entry', [GroupBookingPageWidgetController::class, 'view'])
                    ->name('entry');

                Route::get('available-slots', [GroupBookingPageWidgetController::class, 'availableSlots'])
                    ->name('available-slots');

                Route::post('book', [GroupBookingPageWidgetController::class, 'book'])
                    ->name('book');
            });

        // Shared asset serving route for both personal and group booking widgets
        // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
        // NGINX has been configured to route all requests for assets under /widgets to the application
        Route::get('{file?}', [PersonalBookingPageWidgetController::class, 'asset'])
            ->where('file', '(.*)')
            ->name('asset');
    });
