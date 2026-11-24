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

namespace AdvisingApp\Interaction\Filament\Pages;

use AdvisingApp\Interaction\Enums\InteractionManagementTab;
use AdvisingApp\Interaction\Livewire\InteractionDriversTable;
use AdvisingApp\Interaction\Livewire\InteractionInitiativesTable;
use AdvisingApp\Interaction\Livewire\InteractionOutcomesTable;
use AdvisingApp\Interaction\Livewire\InteractionRelationsTable;
use AdvisingApp\Interaction\Livewire\InteractionStatusesTable;
use AdvisingApp\Interaction\Livewire\InteractionTypesTable;
use App\Enums\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

class InteractionManagement extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Interaction Management';

    protected static ?string $title = 'Interaction Management';

    protected static ?int $navigationSort = 80;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    /**
     * @return string | array<string>
     */
    public static function getNavigationItemActiveRoutePattern(): string | array
    {
        return [
            static::getRouteName(),
            ...collect(InteractionManagementTab::cases())
                ->map(fn (InteractionManagementTab $tab): string => $tab->getResource()::getRouteBaseName() . '.*')
                ->all(),
        ];
    }

    public static function canAccess(): bool
    {
        return collect(InteractionManagementTab::cases())
            ->contains(fn (InteractionManagementTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        if (! InteractionManagementTab::tryFrom($this->activeTab ?? '')?->canView()) {
            $this->activeTab = collect(InteractionManagementTab::cases())
                ->first(fn (InteractionManagementTab $tab): bool => $tab->canView())?->value;
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        InteractionManagementTab::Initiatives->value => $this->makeTab(InteractionManagementTab::Initiatives)
                            ->schema([
                                Livewire::make(InteractionInitiativesTable::class),
                            ]),
                        InteractionManagementTab::Drivers->value => $this->makeTab(InteractionManagementTab::Drivers)
                            ->schema([
                                Livewire::make(InteractionDriversTable::class),
                            ]),
                        InteractionManagementTab::Outcomes->value => $this->makeTab(InteractionManagementTab::Outcomes)
                            ->schema([
                                Livewire::make(InteractionOutcomesTable::class),
                            ]),
                        InteractionManagementTab::Relations->value => $this->makeTab(InteractionManagementTab::Relations)
                            ->schema([
                                Livewire::make(InteractionRelationsTable::class),
                            ]),
                        InteractionManagementTab::Statuses->value => $this->makeTab(InteractionManagementTab::Statuses)
                            ->schema([
                                Livewire::make(InteractionStatusesTable::class),
                            ]),
                        InteractionManagementTab::Types->value => $this->makeTab(InteractionManagementTab::Types)
                            ->schema([
                                Livewire::make(InteractionTypesTable::class),
                            ]),
                    ]),
            ]);
    }

    protected function makeTab(InteractionManagementTab $tab): Tab
    {
        return Tab::make($tab->getLabel())
            ->visible(fn (): bool => $tab->canView());
    }
}
