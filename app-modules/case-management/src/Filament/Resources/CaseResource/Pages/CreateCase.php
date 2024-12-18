<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseResource\Pages;

use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Division\Models\Division;
use App\Filament\Forms\Components\EducatableSelect;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CreateCase extends CreateRecord
{
    protected static string $resource = CaseResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('division_id')
                    ->relationship('division', 'name')
                    ->label('Division')
                    ->required()
                    ->exists((new Division())->getTable(), 'id'),
                Select::make('status_id')
                    ->relationship('status', 'name')
                    ->label('Status')
                    ->options(fn () => CaseStatus::query()
                        ->orderBy('classification')
                        ->orderBy('name')
                        ->get(['id', 'name', 'classification'])
                        ->groupBy(fn (CaseStatus $status) => $status->classification->getlabel())
                        ->map(fn (Collection $group) => $group->pluck('name', 'id')))
                    ->required()
                    ->exists((new CaseStatus())->getTable(), 'id'),
                Grid::make()
                    ->schema([
                        Select::make('type_id')
                            ->options(CaseType::pluck('name', 'id'))
                            ->afterStateUpdated(fn (Set $set) => $set('priority_id', null))
                            ->label('Type')
                            ->required()
                            ->live()
                            ->exists(CaseType::class, 'id'),
                        Select::make('priority_id')
                            ->relationship(
                                name: 'priority',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Get $get, Builder $query) => $query->where('type_id', $get('type_id'))->orderBy('order'),
                            )
                            ->label('Priority')
                            ->required()
                            ->exists(CasePriority::class, 'id')
                            ->visible(fn (Get $get): bool => filled($get('type_id'))),
                    ]),
                Textarea::make('close_details')
                    ->label('Close Details/Description')
                    ->nullable()
                    ->string(),
                Textarea::make('res_details')
                    ->label('Internal Case Details')
                    ->nullable()
                    ->string(),
                EducatableSelect::make('respondent')
                    ->label('Related To')
                    ->required()
                    ->hiddenOn([RelationManager::class, ManageRelatedRecords::class]),
            ]);
    }
}
