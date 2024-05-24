<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Portal\Http\Middleware\AuthenticateIfRequiredByPortalDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;
use AdvisingApp\Portal\Settings\PortalSettings;
use App\Multitenancy\Http\Middleware\NeedsTenant;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use AdvisingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEnabled;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalController;
use AdvisingApp\Portal\Http\Middleware\EnsureKnowledgeManagementPortalIsEmbeddableAndAuthorized;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalLogoutController;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalSearchController;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalArticleController;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalCategoryController;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalAuthenticateController;
use AdvisingApp\Portal\Http\Controllers\KnowledgeManagement\KnowledgeManagementPortalRequestAuthenticationController;

Route::prefix('api')
    ->name('api.')
    ->middleware([
        'api',
        NeedsTenant::class,
        EnsureKnowledgeManagementPortalIsEnabled::class,
        EnsureKnowledgeManagementPortalIsEmbeddableAndAuthorized::class,
    ])
    ->group(function () {
        Route::middleware(['auth:sanctum'])
            ->group(function () {
                Route::get('/user', function (Request $request) {
                    $user = $request->user('student') ?? $request->user('prospect') ?? null;

                    if ($user?->tokenCan('knowledge-management-portal')) {
                        return $user;
                    }

                    abort(Response::HTTP_FORBIDDEN);
                })->name('user.auth-check');
            });

        Route::prefix('portal/knowledge-management')
            ->name('portal.knowledge-management.')
            ->group(function () {
                Route::get('/', [KnowledgeManagementPortalController::class, 'show'])
                    ->middleware(['signed:relative'])
                    ->name('define');

                Route::middleware([AuthenticateIfRequiredByPortalDefinition::class])
                    ->group(function () {
                        Route::post('/authenticate/logout', KnowledgeManagementPortalLogoutController::class)
                            ->name('logout');

                        Route::post('/search', [KnowledgeManagementPortalSearchController::class, 'get'])
                            ->middleware(['signed:relative'])
                            ->name('search');

                        Route::get('/categories', [KnowledgeManagementPortalCategoryController::class, 'index'])
                            ->name('category.index');

                        Route::get('/categories/{category}', [KnowledgeManagementPortalCategoryController::class, 'show'])
                            ->name('category.show');

                        Route::get('/categories/{category}/articles/{article}', [KnowledgeManagementPortalArticleController::class, 'show'])
                            ->name('article.show');
                    });

                Route::post('/authenticate/request', KnowledgeManagementPortalRequestAuthenticationController::class)
                    ->middleware(['signed:relative'])
                    ->name('request-authentication');

                Route::post('/authenticate/{authentication}', KnowledgeManagementPortalAuthenticateController::class)
                    ->middleware(['signed:relative', EnsureFrontendRequestsAreStateful::class])
                    ->name('authenticate.embedded');
            });
    });
