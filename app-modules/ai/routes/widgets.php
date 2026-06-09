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

use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\AuthenticationConfirmController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\AuthenticationRefreshController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\CustomerAdvisorBroadcastController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\CustomerAdvisorResourceController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\CustomerAdvisorResourcesController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\FinishAdvisorThreadController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\RegisterProspectController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\RequestAuthenticationController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\SendAdvisorMessageController as SendCustomerAdvisorMessageController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\ShowAdvisorController;
use AdvisingApp\Ai\Http\Controllers\CustomerAdvisors\StartAdvisorThreadController;
use AdvisingApp\Ai\Http\Middleware\CustomerAdvisorAuthorization;
use AdvisingApp\Ai\Http\Middleware\CustomerAdvisorWidgetCors;
use AdvisingApp\Ai\Http\Middleware\EnsureCustomerAdvisorEmbedIsEnabled;
use AdvisingApp\Ai\Http\Middleware\EnsureCustomerAdvisorRequestComingFromAuthorizedDomain;
use AdvisingApp\Ai\Models\CustomerAdvisor;
use App\Http\Middleware\EncryptCookies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$registerAdvisorRoutes = function () {
    Route::prefix('api/{advisor}')
        ->name('api.')
        ->middleware([
            EnsureCustomerAdvisorEmbedIsEnabled::class,
            EnsureCustomerAdvisorRequestComingFromAuthorizedDomain::class,
        ])
        ->group(function () {
            Route::get('/', CustomerAdvisorResourcesController::class)
                ->name('assets');

            Route::post('/entry', ShowAdvisorController::class)
                ->name('entry');

            Route::match(
                ['GET', 'POST', 'HEAD'],
                '/broadcasting/auth',
                [CustomerAdvisorBroadcastController::class, 'auth']
            )
                ->middleware([CustomerAdvisorAuthorization::class])
                ->name('broadcasting.auth');

            Route::post('/authenticate/request', RequestAuthenticationController::class)
                ->middleware(['signed'])
                ->name('authentication.request');

            Route::post('/authenticate/register', RegisterProspectController::class)
                ->middleware(['signed'])
                ->name('register-prospect');

            Route::post('/authenticate/confirm/{authentication}', AuthenticationConfirmController::class)
                ->middleware(['signed'])
                ->name('authentication.confirm');

            Route::post('/authenticate/refresh', AuthenticationRefreshController::class)
                ->middleware(['signed'])
                ->name('authentication.refresh');

            Route::post('/threads/start', StartAdvisorThreadController::class)
                ->middleware([
                    'signed',
                    CustomerAdvisorAuthorization::class,
                ])
                ->name('threads.start');

            Route::post('/messages', SendCustomerAdvisorMessageController::class)
                ->middleware([
                    'signed',
                    CustomerAdvisorAuthorization::class,
                ])
                ->name('messages.send');

            Route::post('/threads/{thread}/finish', FinishAdvisorThreadController::class)
                ->middleware([
                    'signed',
                    CustomerAdvisorAuthorization::class,
                ])
                ->name('threads.finish');

            // Handle preflight CORS requests for all routes in this group
            // MUST remain the last route in this group
            Route::options('/{any}', function (Request $request, CustomerAdvisor $advisor) {
                return response()->noContent();
            })
                ->where('any', '.*')
                ->name('preflight');
        });

    // This route MUST remain at /widgets/... in order to catch requests to asset files and return the correct headers
    // NGINX has been configured to route all requests for assets under /widgets to the application
    Route::get('{file?}', CustomerAdvisorResourceController::class)
        ->where('file', '(.*)')
        ->name('asset');
};

// Primary routes
Route::middleware([
    'api',
    EncryptCookies::class,
    CustomerAdvisorWidgetCors::class,
])
    ->name('widgets.ai.customer-advisors.')
    ->prefix('widgets/ai/customer-advisors')
    ->group($registerAdvisorRoutes);

// Legacy support
Route::middleware([
    'api',
    EncryptCookies::class,
    CustomerAdvisorWidgetCors::class,
])
    ->name('widgets.ai.qna-advisors.')
    ->prefix('widgets/ai/qna-advisors')
    ->group($registerAdvisorRoutes);
