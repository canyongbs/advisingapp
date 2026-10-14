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

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Engagement\Enums\InboxTab;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Livewire\InboxTable;
use AdvisingApp\Engagement\Livewire\SentItemsTable;
use App\Enums\Feature;
use App\Enums\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use UnitEnum;

class Inbox extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Crm;

    protected static ?string $navigationLabel = 'Unified Inbox';

    protected static ?string $title = 'Unified Inbox';

    protected static ?int $navigationSort = 10;

    #[Url(as: 'tab')]
    public string $activeTab = InboxTab::Inbox->value;

    public static function canAccess(): bool
    {
        if (! Gate::check(Feature::UnifiedInbox->getGateName())) {
            return false;
        }

        return collect(InboxTab::cases())
            ->contains(fn (InboxTab $tab): bool => $tab->canView());
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        InboxTab::Inbox->value => $this->makeTab(InboxTab::Inbox)
                            ->schema([
                                Livewire::make(InboxTable::class),
                            ]),
                        InboxTab::SentItems->value => $this->makeTab(InboxTab::SentItems)
                            ->schema([
                                Livewire::make(SentItemsTable::class),
                            ]),
                    ]),
            ]);
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0]
                ->isActiveWhen(fn (): bool => request()->routeIs(
                    static::getNavigationItemActiveRoutePattern(),
                    ViewEngagementResponse::getNavigationItemActiveRoutePattern(),
                    ViewEngagement::getNavigationItemActiveRoutePattern(),
                )),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            SendEngagementAction::make()
                ->label('New')
                ->icon(null),
        ];
    }

    protected function makeTab(InboxTab $tab): Tab
    {
        return Tab::make($tab->getLabel())
            ->visible(fn (): bool => $tab->canView());
    }
}
