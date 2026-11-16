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

namespace AdvisingApp\Application;

use AdvisingApp\Application\Enums\OnlineAdmissionsTab;
use AdvisingApp\Application\Filament\Pages\OnlineAdmissions;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStates\ApplicationSubmissionStateResource;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

class ApplicationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'application';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'AdvisingApp\\Application\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'AdvisingApp\\Application\\Filament\\Pages'
            )
            ->authenticatedTenantRoutes(function (): void {
                // The submission states resource used to live in the `OnlineAdmissions` cluster at
                // `online-admissions/application-submission-states/...`. These redirects keep bookmarks and
                // links to the old cluster URLs (list, create, view, edit) working after the cluster was removed.
                Route::prefix('online-admissions/application-submission-states')
                    ->group(function (): void {
                        Route::get('/', fn (): RedirectResponse => redirect()->to(
                            OnlineAdmissions::getUrl(['tab' => OnlineAdmissionsTab::SubmissionStates->value]),
                        ));

                        Route::get('/create', fn (): RedirectResponse => redirect()->to(
                            ApplicationSubmissionStateResource::getUrl('create'),
                        ));

                        Route::get('/{record}', fn (string $record): RedirectResponse => redirect()->to(
                            ApplicationSubmissionStateResource::getUrl('view', ['record' => $record]),
                        ));

                        Route::get('/{record}/edit', fn (string $record): RedirectResponse => redirect()->to(
                            ApplicationSubmissionStateResource::getUrl('edit', ['record' => $record]),
                        ));
                    });
            });
    }

    public function boot(Panel $panel): void {}
}
