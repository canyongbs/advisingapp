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

use AdvisingApp\Form\Http\Controllers\FormWidgetController;
use AdvisingApp\Form\Http\Middleware\EnsureFormsFeatureIsActive;
use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use AdvisingApp\Form\Http\Middleware\FormsWidgetCors;
use AdvisingApp\Form\Models\Form;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'api',
    EncryptCookies::class,
    EnsureFormsFeatureIsActive::class,
    FormsWidgetCors::class,
])
    ->prefix('widgets/forms')
    ->name('widgets.forms.')
    ->group(function () {
        Route::get('form-upload-url', [FormWidgetController::class, 'uploadFormFiles'])
            ->name('form-upload-url');

        Route::prefix('api/{form}')
            ->name('api.')
            ->middleware([
                EnsureSubmissibleIsEmbeddableAndAuthorized::class . ':form',
            ])
            ->group(function () {
                Route::get('/', [FormWidgetController::class, 'assets'])
                    ->name('assets');

                Route::get('entry', [FormWidgetController::class, 'view'])
                    ->name('entry');
                Route::post('authenticate/request', [FormWidgetController::class, 'requestAuthentication'])
                    ->middleware(['signed'])
                    ->name('request-authentication');
                Route::post('authenticate/{authentication}', [FormWidgetController::class, 'authenticate'])
                    ->middleware(['signed'])
                    ->name('authenticate');
                Route::post('submit', [FormWidgetController::class, 'store'])
                    ->middleware(['signed'])
                    ->name('submit');
                Route::post('register', [FormWidgetController::class, 'registerProspect'])
                    ->middleware(['signed'])
                    ->name('register-prospect');

                // Handle preflight CORS requests for all routes in this group
                // MUST remain the last route in this group
                Route::options('/{any}', function (Request $request, Form $form) {
                    return response()->noContent();
                })
                    ->where('any', '.*')
                    ->name('preflight');
            });

        // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
        // NGINX has been configured to route all requests for assets under /widgets to the application
        Route::get('{file?}', [FormWidgetController::class, 'asset'])
            ->where('file', '(.*)')
            ->name('asset');
    });

Route::prefix('api')
    ->middleware([
        'web',
        'auth',
        EnsureFormsFeatureIsActive::class,
    ])
    ->group(function () {
        Route::get('/forms/{form}/preview', [FormWidgetController::class, 'preview'])
            ->name('forms.api.preview');
    });
