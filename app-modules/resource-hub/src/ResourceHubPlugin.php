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

namespace AdvisingApp\ResourceHub;

use AdvisingApp\ResourceHub\Enums\ResourceHubTab;
use AdvisingApp\ResourceHub\Filament\Pages\ResourceHub;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubCategories\ResourceHubCategoryResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubQualities\ResourceHubQualityResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubStatuses\ResourceHubStatusResource;
use AdvisingApp\ResourceHub\Filament\Widgets\ResourceHubArticleConcernsTable;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;

class ResourceHubPlugin implements Plugin
{
    public function getId(): string
    {
        return 'resource-hub';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverResources(
                in: __DIR__ . '/Filament/Resources',
                for: 'AdvisingApp\\ResourceHub\\Filament\\Resources'
            )
            ->discoverPages(
                in: __DIR__ . '/Filament/Pages',
                for: 'AdvisingApp\\ResourceHub\\Filament\\Pages'
            )
            ->discoverLivewireComponents(
                in: __DIR__ . '/Livewire',
                for: 'AdvisingApp\\ResourceHub\\Livewire'
            )
            ->livewireComponents([
                ResourceHubArticleConcernsTable::class,
            ])
            ->authenticatedTenantRoutes(function (): void {
                // These resources used to live in the `ResourceHub` cluster at `resource-hub/<resource>/...`.
                // These redirects keep bookmarks and links to the old cluster URLs (list, create, view, edit)
                // working after the cluster was removed.
                foreach ([
                    'resource-hub-categories' => [ResourceHubTab::Categories, ResourceHubCategoryResource::class],
                    'resource-hub-qualities' => [ResourceHubTab::Qualities, ResourceHubQualityResource::class],
                    'resource-hub-statuses' => [ResourceHubTab::Statuses, ResourceHubStatusResource::class],
                ] as $segment => [$tab, $resource]) {
                    Route::prefix("resource-hub/{$segment}")
                        ->group(function () use ($tab, $resource): void {
                            Route::get('/', fn (): RedirectResponse => redirect()->to(
                                ResourceHub::getUrl([
                                    ...request()->query(),
                                    'tab' => $tab->value,
                                ]),
                            ));

                            Route::get('/create', fn (): RedirectResponse => redirect()->to(
                                $resource::getUrl('create', request()->query()),
                            ));

                            Route::get('/{record}', fn (string $record): RedirectResponse => redirect()->to(
                                $resource::getUrl('view', [
                                    ...request()->query(),
                                    'record' => $record,
                                ]),
                            ));

                            Route::get('/{record}/edit', fn (string $record): RedirectResponse => redirect()->to(
                                $resource::getUrl('edit', [
                                    ...request()->query(),
                                    'record' => $record,
                                ]),
                            ));
                        });
                }
            });
    }

    public function boot(Panel $panel): void {}
}
