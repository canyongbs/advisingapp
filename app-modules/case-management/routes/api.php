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

use AdvisingApp\CaseManagement\Http\Controllers\CaseFeedbackFormWidgetController;
use AdvisingApp\CaseManagement\Http\Controllers\CaseFormWidgetController;
use AdvisingApp\CaseManagement\Http\Middleware\CaseTypeFeedbackIsOn;
use AdvisingApp\CaseManagement\Http\Middleware\EnsureCaseManagementFeatureIsActive;
use AdvisingApp\CaseManagement\Http\Middleware\FeedbackManagementIsOn;
use AdvisingApp\Form\Http\Middleware\EnsureSubmissibleIsEmbeddableAndAuthorized;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::prefix('api')
    ->middleware([
        EnsureFrontendRequestsAreStateful::class,
        'api',
    ])
    ->group(function () {
        Route::prefix('case-forms')
            ->name('case-forms.')
            ->middleware([
                EnsureCaseManagementFeatureIsActive::class,
                EnsureSubmissibleIsEmbeddableAndAuthorized::class . ':caseForm',
            ])
            ->group(function () {
                Route::get('/{caseForm}', [CaseFormWidgetController::class, 'view'])
                    ->middleware(['signed:relative'])
                    ->name('define');
                Route::post('/{caseForm}/authenticate/request', [CaseFormWidgetController::class, 'requestAuthentication'])
                    ->middleware(['signed:relative'])
                    ->name('request-authentication');
                Route::post('/{caseForm}/authenticate/{authentication}', [CaseFormWidgetController::class, 'authenticate'])
                    ->middleware(['signed:relative'])
                    ->name('authenticate');
                Route::post('/{caseForm}/submit', [CaseFormWidgetController::class, 'store'])
                    ->middleware(['signed:relative'])
                    ->name('submit');
            });

        Route::prefix('cases/{case}/feedback')
            ->name('cases.feedback.')
            ->middleware([
                FeedbackManagementIsOn::class,
                CaseTypeFeedbackIsOn::class,
            ])
            ->group(function () {
                Route::get('/', [CaseFeedbackFormWidgetController::class, 'view'])
                    ->name('define');
                Route::post('/submit', [CaseFeedbackFormWidgetController::class, 'store'])
                    ->middleware(['auth:sanctum'])
                    ->name('submit');
            });
    });
