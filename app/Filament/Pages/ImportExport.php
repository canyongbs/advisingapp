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

namespace App\Filament\Pages;

use AdvisingApp\StudentDataModel\Livewire\StudentDataImportsTable;
use App\Enums\ImportExportTab;
use App\Enums\NavigationGroup;
use App\Livewire\ExportsTable;
use App\Livewire\ImportsTable;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

class ImportExport extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::DataAndAnalytics;

    protected static ?string $navigationLabel = 'Import/Export';

    protected static ?string $title = 'Import/Export';

    protected static ?int $navigationSort = 30;

    #[Url(as: 'tab')]
    public string $activeTab = ImportExportTab::Import->value;

    public static function canAccess(): bool
    {
        return collect(ImportExportTab::cases())
            ->contains(fn (ImportExportTab $tab): bool => $tab->canView());
    }

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        ImportExportTab::Import->value => $this->makeTab(ImportExportTab::Import)
                            ->schema([
                                Livewire::make(ImportsTable::class),
                            ]),
                        ImportExportTab::Export->value => $this->makeTab(ImportExportTab::Export)
                            ->schema([
                                Livewire::make(ExportsTable::class),
                            ]),
                        ImportExportTab::StudentSync->value => $this->makeTab(ImportExportTab::StudentSync)
                            ->schema([
                                Livewire::make(StudentDataImportsTable::class),
                            ]),
                    ]),
            ]);
    }

    public function updatedActiveTab(): void
    {
        $this->authorizeAccess();
    }

    protected function authorizeAccess(): void
    {
        if (ImportExportTab::tryFrom($this->activeTab)?->canView()) {
            return;
        }

        $this->activeTab = collect(ImportExportTab::cases())
            ->first(fn (ImportExportTab $tab): bool => $tab->canView())
            ->value ?? abort(403);
    }

    protected function makeTab(ImportExportTab $tab): Tab
    {
        return Tab::make($tab->getLabel())
            ->visible(fn (): bool => $tab->canView());
    }
}
