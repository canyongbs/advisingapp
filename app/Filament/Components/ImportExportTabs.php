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

namespace App\Filament\Components;

use AdvisingApp\StudentDataModel\Filament\Pages\ManageStudentSyncs;
use App\Filament\Pages\ExportPage;
use App\Filament\Pages\ImportPage;
use Filament\Schemas\Components\Component;
use Filament\Support\Components\Contracts\HasEmbeddedView;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class ImportExportTabs extends Component implements HasEmbeddedView
{
    final public function __construct(
        protected string $activeTab,
    ) {}

    public static function make(string $activeTab): static
    {
        $static = app(static::class, ['activeTab' => $activeTab]);
        $static->configure();

        return $static;
    }

    public function toEmbeddedHtml(): string
    {
        $content = $this->getChildSchema()?->toHtml() ?? '';

        $tabs = $this->getTabs();

        if ($tabs->isEmpty()) {
            return $content;
        }

        return Blade::render(
            <<<'BLADE'
            <div class="fi-sc-tabs fi-contained">
                <x-filament::tabs :contained="true">
                    @foreach ($tabs as $key => $tab)
                        <x-filament::tabs.item
                            tag="a"
                            :active="$activeTab === $key"
                            :href="$tab['page']::getUrl()"
                            :spa-mode="$spaMode"
                        >
                            {{ $tab['label'] }}
                        </x-filament::tabs.item>
                    @endforeach
                </x-filament::tabs>

                <div class="fi-sc-tabs-tab fi-active">
                    {!! $content !!}
                </div>
            </div>
            BLADE,
            [
                'tabs' => $tabs,
                'activeTab' => $this->activeTab,
                'content' => $content,
                'spaMode' => FilamentView::hasSpaMode(),
            ],
        );
    }

    /**
     * @return Collection<string, array{label: string, page: string}>
     */
    protected function getTabs(): Collection
    {
        return collect([
            'import' => ['label' => 'Import', 'page' => ImportPage::class],
            'export' => ['label' => 'Export', 'page' => ExportPage::class],
            'student-sync' => ['label' => 'Student Sync', 'page' => ManageStudentSyncs::class],
        ])->filter(fn (array $tab): bool => $tab['page']::canAccess());
    }
}
