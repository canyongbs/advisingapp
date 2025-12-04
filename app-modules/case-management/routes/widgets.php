<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\CaseManagement\Http\Controllers\CaseFeedbackFormWidgetController;
use AdvisingApp\CaseManagement\Http\Controllers\CaseFormWidgetController;
use AdvisingApp\CaseManagement\Http\Middleware\CaseFormsWidgetCors;
use AdvisingApp\CaseManagement\Http\Middleware\CaseTypeFeedbackIsOn;
use AdvisingApp\CaseManagement\Http\Middleware\EnsureCaseManagementFeatureIsActive;
use AdvisingApp\CaseManagement\Http\Middleware\FeedbackManagementIsOn;
use AdvisingApp\CaseManagement\Models\CaseForm;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// Case Form Widget Routes (with CORS for external embedding)
Route::middleware([
    'api',
    EncryptCookies::class,
    EnsureCaseManagementFeatureIsActive::class,
    CaseFormsWidgetCors::class,
])
    ->prefix('widgets/case-forms')
    ->name('widgets.case-forms.')
    ->group(function () {
        Route::prefix('api/{caseForm}')
            ->name('api.')
            ->middleware([
                EnsureSubmissibleIsEmbeddableAndAuthorized::class . ':caseForm',
            ])
            ->group(function () {
                Route::get('/', [CaseFormWidgetController::class, 'assets'])
                    ->name('assets');

                Route::get('entry', [CaseFormWidgetController::class, 'view'])
                    ->name('entry');
                Route::post('authenticate/request', [CaseFormWidgetController::class, 'requestAuthentication'])
                    ->middleware(['signed'])
                    ->name('request-authentication');
                Route::post('authenticate/{authentication}', [CaseFormWidgetController::class, 'authenticate'])
                    ->middleware(['signed'])
                    ->name('authenticate');
                Route::post('submit', [CaseFormWidgetController::class, 'store'])
                    ->middleware(['signed'])
                    ->name('submit');

                // Handle preflight CORS requests for all routes in this group
                // MUST remain the last route in this group
                Route::options('/{any}', function (Request $request, CaseForm $caseForm) {
                    return response()->noContent();
                })
                    ->where('any', '.*')
                    ->name('preflight');
            });

        // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
        // NGINX has been configured to route all requests for assets under /widgets to the application
        Route::get('{file?}', [CaseFormWidgetController::class, 'asset'])
            ->where('file', '(.*)')
            ->name('asset');
    });

// Case Feedback Form Widget Routes (internal use only, with Sanctum)
Route::middleware([
    'api',
    EncryptCookies::class,
    EnsureFrontendRequestsAreStateful::class,
])
    ->prefix('widgets/case-feedback-forms')
    ->name('widgets.case-feedback-forms.')
    ->group(function () {
        Route::prefix('api/{case}')
            ->name('api.')
            ->middleware([
                FeedbackManagementIsOn::class,
                CaseTypeFeedbackIsOn::class,
            ])
            ->group(function () {
                Route::get('/', [CaseFeedbackFormWidgetController::class, 'assets'])
                    ->name('assets');

                Route::get('entry', [CaseFeedbackFormWidgetController::class, 'view'])
                    ->name('entry');
                Route::post('submit', [CaseFeedbackFormWidgetController::class, 'store'])
                    ->middleware(['signed'])
                    ->name('submit');

                // Handle preflight CORS requests for all routes in this group
                // MUST remain the last route in this group
                Route::options('/{any}', function (Request $request, CaseModel $case) {
                    return response()->noContent();
                })
                    ->where('any', '.*')
                    ->name('preflight');
            });

        // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
        // NGINX has been configured to route all requests for assets under /widgets to the application
        Route::get('{file?}', [CaseFeedbackFormWidgetController::class, 'asset'])
            ->where('file', '(.*)')
            ->name('asset');
    });
