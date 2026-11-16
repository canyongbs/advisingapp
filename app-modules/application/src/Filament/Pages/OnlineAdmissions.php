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

namespace AdvisingApp\Application\Filament\Pages;

use AdvisingApp\Application\Enums\OnlineAdmissionsTab;
use AdvisingApp\Application\Filament\Resources\ApplicationSubmissionStates\Tables\ApplicationSubmissionStatesTable;
use App\Enums\NavigationGroup;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Url;
use UnitEnum;

class OnlineAdmissions extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Settings;

    protected static ?string $navigationLabel = 'Admissions';

    protected static ?string $title = 'Admissions';

    protected static ?int $navigationSort = 10;

    #[Url(as: 'tab')]
    public ?string $activeTab = null;

    public static function canAccess(): bool
    {
        return collect(OnlineAdmissionsTab::cases())
            ->contains(fn (OnlineAdmissionsTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        if (! OnlineAdmissionsTab::tryFrom($this->activeTab ?? '')?->canView()) {
            $this->activeTab = collect(OnlineAdmissionsTab::cases())
                ->first(fn (OnlineAdmissionsTab $tab): bool => $tab->canView())?->value;
        }
    }

    public function table(Table $table): Table
    {
        return ApplicationSubmissionStatesTable::configure($table);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        OnlineAdmissionsTab::SubmissionStates->value => $this->makeTab(OnlineAdmissionsTab::SubmissionStates)
                            ->schema([
                                EmbeddedTable::make(),
                            ]),
                    ]),
            ]);
    }

    protected function makeTab(OnlineAdmissionsTab $tab): Tab
    {
        return Tab::make($tab->getLabel())
            ->visible(fn (): bool => $tab->canView());
    }
}
