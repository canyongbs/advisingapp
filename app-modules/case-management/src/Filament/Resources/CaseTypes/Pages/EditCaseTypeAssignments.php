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

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseTypes\Pages;

use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypes\CaseTypeResource;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Rules\CaseTypeAssignmentsIndividualUserMustBeAManager;
use App\Filament\Forms\Components\Heading;
use App\Filament\Forms\Components\Paragraph;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EditCaseTypeAssignments extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = CaseTypeResource::class;

    protected static ?string $title = 'Assignments';

    protected static ?string $navigationLabel = 'Assignments';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns()
                    ->schema([
                        Heading::make()
                            ->content('Assignments'),
                        Paragraph::make()
                            ->content('This page is used to configure the assignment methodology for this case type.'),
                        Radio::make('assignment_type')
                            ->live()
                            ->columnSpanFull()
                            ->label(
                                new HtmlString(
                                    view('case-management::filament.forms.assignment-type-label')->render()
                                )
                            )
                            ->options(CaseTypeAssignmentTypes::class)
                            ->enum(CaseTypeAssignmentTypes::class)
                            ->descriptions(
                                collect(CaseTypeAssignmentTypes::cases())
                                    ->mapWithKeys(fn (CaseTypeAssignmentTypes $assignmentType): array => [$assignmentType->value => $assignmentType->getDescription()])
                                    ->toArray()
                            )
                            ->required(),
                        Select::make('assignment_type_individual_id')
                            ->label('Assignment Individual')
                            ->columnSpanFull()
                            ->relationship(
                                name: 'assignmentTypeIndividual',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query) => $query->whereRelation(
                                    'team.manageableCaseTypes',
                                    'case_types.id',
                                    $this->record->getKey(),
                                )
                            )
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->required()
                            ->rules(fn (CaseType $record) => [new CaseTypeAssignmentsIndividualUserMustBeAManager($record)])
                            ->visible(fn (Get $get) => $get('assignment_type') === CaseTypeAssignmentTypes::Individual),
                    ]),
            ]);
    }
}
