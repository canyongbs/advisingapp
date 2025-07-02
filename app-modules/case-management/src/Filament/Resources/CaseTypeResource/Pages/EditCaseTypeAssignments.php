<?php

namespace AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages;

use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\CaseManagement\Rules\CaseTypeAssignmentsIndividualUserMustBeAManager;
use App\Filament\Forms\Components\Heading;
use App\Filament\Forms\Components\Paragraph;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class EditCaseTypeAssignments extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = CaseTypeResource::class;

    protected static ?string $title = 'Assignments';

    public function getRelationManagers(): array
    {
        // Needed to prevent Filament from loading the relation managers on this page.
        return [];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                            ->visible(fn (Get $get) => $get('assignment_type') === CaseTypeAssignmentTypes::Individual->value),
                    ]),
            ]);
    }
}
