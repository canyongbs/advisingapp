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
use AdvisingApp\StudentDataModel\Settings\ManageStudentConfigurationSettings;
use App\Enums\NavigationGroup;
use App\Livewire\ExportsTable;
use App\Livewire\ImportsTable;
use App\Models\User;
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
    public string $activeTab = 'import';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        if ($user->can('export_hub.view-any')) {
            return true;
        }

        return app(ManageStudentConfigurationSettings::class)->is_enabled
            && $user->can('record_sync.view-any');
    }

    public function mount(): void
    {
        if ($this->isTabVisible($this->activeTab)) {
            return;
        }

        $this->activeTab = collect(['import', 'export', 'student-sync'])
            ->first(fn (string $tab): bool => $this->isTabVisible($tab)) ?? 'import';
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->livewireProperty('activeTab')
                ->tabs([
                    'import' => Tab::make('Import')
                        ->visible(fn (): bool => $this->isTabVisible('import'))
                        ->schema([
                            Livewire::make(ImportsTable::class),
                        ]),
                    'export' => Tab::make('Export')
                        ->visible(fn (): bool => $this->isTabVisible('export'))
                        ->schema([
                            Livewire::make(ExportsTable::class),
                        ]),
                    'student-sync' => Tab::make('Student Sync')
                        ->visible(fn (): bool => $this->isTabVisible('student-sync'))
                        ->schema([
                            Livewire::make(StudentDataImportsTable::class),
                        ]),
                ]),
        ]);
    }

    protected function isTabVisible(string $tab): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return match ($tab) {
            'import', 'export' => $user->can('export_hub.view-any'),
            'student-sync' => app(ManageStudentConfigurationSettings::class)->is_enabled
                && $user->can('record_sync.view-any'),
            default => false,
        };
    }
}
