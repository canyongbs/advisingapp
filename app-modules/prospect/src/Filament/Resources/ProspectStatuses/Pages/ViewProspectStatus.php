<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectStatuses\Pages;

use AdvisingApp\Prospect\Filament\Resources\ProspectStatuses\ProspectStatusResource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Features\ProspectStatusFeature;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

class ViewProspectStatus extends ViewRecord
{
    protected static string $resource = ProspectStatusResource::class;

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::PAGE_HEADER_ACTIONS_AFTER,
            fn (): View => view('components.page-header-action-lock-icon', [
                'condition' => function () {
                    return $this->getRecord()?->is_system_protected;
                },
                'identifier' => 'prospect_status_system_protected',
                'tooltip' => 'This record is protected as it is a system status.',
            ]),
            scopes: static::class,
        );
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('classification')
                            ->label('Classification'),
                        //TODO: FeatureFlag Cleanup - Remove TextEntry when you remove feature flag and just use ColorEntry
                        TextEntry::make('color')
                            ->label('Color')
                            ->badge()
                            ->visible(! ProspectStatusFeature::active())
                            ->color(fn (ProspectStatus $prospectStatus) => $prospectStatus->color->value),
                        ColorEntry::make('color')
                            ->visible(ProspectStatusFeature::active())
                            ->label('Color'),
                        TextEntry::make('sort')
                            ->numeric(),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
