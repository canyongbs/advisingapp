<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseUpdateResource\Pages;

use AdvisingApp\CaseManagement\Enums\CaseUpdateDirection;
use AdvisingApp\CaseManagement\Filament\Concerns\CaseUpdateBreadcrumbs;
use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use AdvisingApp\CaseManagement\Filament\Resources\CaseUpdateResource;
use AdvisingApp\CaseManagement\Models\CaseUpdate;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewCaseUpdate extends ViewRecord
{
    use CaseUpdateBreadcrumbs;

    protected static string $resource = CaseUpdateResource::class;

    protected static ?string $breadcrumb = 'View';

    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        TextEntry::make('case.case_number')
                            ->label('Case')
                            ->url(fn (CaseUpdate $caseUpdate): string => CaseResource::getUrl('view', ['record' => $caseUpdate->case]))
                            ->color('primary'),
                        IconEntry::make('internal')
                            ->boolean(),
                        TextEntry::make('direction')
                            ->icon(fn (CaseUpdateDirection $state): string => $state->getIcon())
                            ->formatStateUsing(fn (CaseUpdateDirection $state): string => $state->getLabel()),
                        TextEntry::make('update')
                            ->columnSpanFull(),
                    ])
                    ->columns(),
            ]);
    }
}
