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

namespace AdvisingApp\ResourceHub\Filament\Pages;

use AdvisingApp\ResourceHub\Enums\ResourceHubTab;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubCategories\ResourceHubCategoryResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubQualities\ResourceHubQualityResource;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubStatuses\ResourceHubStatusResource;
use AdvisingApp\ResourceHub\Livewire\ResourceHubCategoriesTable;
use AdvisingApp\ResourceHub\Livewire\ResourceHubQualitiesTable;
use AdvisingApp\ResourceHub\Livewire\ResourceHubStatusesTable;
use App\Enums\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

class ResourceHub extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Resource Hub';

    protected static ?string $title = 'Resource Hub';

    protected static ?int $navigationSort = 110;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    /**
     * @return string | array<string>
     */
    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return [
            static::getRouteName(),
            ResourceHubCategoryResource::getRouteBaseName() . '.*',
            ResourceHubQualityResource::getRouteBaseName() . '.*',
            ResourceHubStatusResource::getRouteBaseName() . '.*',
        ];
    }

    public static function canAccess(): bool
    {
        return collect(ResourceHubTab::cases())
            ->contains(fn (ResourceHubTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        if (! ResourceHubTab::tryFrom($this->activeTab ?? '')?->canView()) {
            $this->activeTab = collect(ResourceHubTab::cases())
                ->first(fn (ResourceHubTab $tab): bool => $tab->canView())?->value;
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        ResourceHubTab::Categories->value => $this->makeTab(ResourceHubTab::Categories)
                            ->schema([
                                Livewire::make(ResourceHubCategoriesTable::class),
                            ]),
                        ResourceHubTab::Qualities->value => $this->makeTab(ResourceHubTab::Qualities)
                            ->schema([
                                Livewire::make(ResourceHubQualitiesTable::class),
                            ]),
                        ResourceHubTab::Statuses->value => $this->makeTab(ResourceHubTab::Statuses)
                            ->schema([
                                Livewire::make(ResourceHubStatusesTable::class),
                            ]),
                    ]),
            ]);
    }

    protected function makeTab(ResourceHubTab $tab): Tab
    {
        return Tab::make($tab->getLabel())
            ->visible(fn (): bool => $tab->canView());
    }
}
