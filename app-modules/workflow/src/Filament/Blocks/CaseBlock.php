<?php

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Division\Models\Division;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;

class CaseBlock extends WorkflowActionBlock
{
    protected Model | string | Closure | null $model = CaseModel::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Case');

        $this->schema($this->createFields());
    }

    /**
     * @return array<int, covariant Field>
     */
    public function generateFields(): array
    {
        return [
            Select::make('division_id')
                ->relationship('division', 'name')
                ->model(CaseModel::class)
                ->default(fn () => auth()->user()->team?->division?->getKey()
                    ?? Division::query()->where('is_default', true)->first()?->getKey())
                ->label('Division')
                ->visible(fn () => Division::count() > 1 && ! Division::where('is_default', true)->exists())
                ->required()
                ->exists((new Division())->getTable(), 'id'),
            Select::make('status_id')
                ->relationship('status', 'name')
                ->model(CaseModel::class)
                ->preload()
                ->label('Status')
                ->required()
                ->exists((new CaseStatus())->getTable(), 'id'),
            Select::make('type_id')
                ->options(CaseType::pluck('name', 'id'))
                ->afterStateUpdated(function (Set $set) {
                    $set('priority_id', null);
                    $set('assigned_to_id', null);
                })
                ->label('Type')
                ->required()
                ->live()
                ->exists(CaseType::class, 'id'),
            Select::make('priority_id')
                ->options(
                    fn (Get $get) => CasePriority::query()
                        ->where('type_id', $get('type_id'))
                        ->orderBy('order')
                        ->pluck('name', 'id')
                )
                ->label('Priority')
                ->required()
                ->exists((new CasePriority())->getTable(), 'id')
                ->visible(fn (Get $get) => filled($get('type_id'))),
            Select::make('assigned_to_id')
                ->label('Assign Case to')
                ->options(function (Get $get) {
                    $caseTypeId = $get('type_id');

                    if (! $caseTypeId) {
                        return [];
                    }

                    $caseType = CaseType::find($caseTypeId);

                    if (! $caseType) {
                        return [];
                    }

                    $managers = User::query()
                        ->whereHas('team.manageableCaseTypes', fn ($q) => $q->where('case_types.id', $caseTypeId))
                        ->pluck('name', 'id')
                        ->toArray();

                    if ($caseType->assignment_type !== CaseTypeAssignmentTypes::None) {
                        return ['automatic' => 'Automatic Assignment'] + $managers;
                    }

                    return $managers;
                })
                ->searchable()
                ->preload()
                ->required(),
            Textarea::make('close_details')
                ->label('Close Details/Description')
                ->nullable()
                ->string(),
            Textarea::make('res_details')
                ->label('Internal Case Details')
                ->nullable()
                ->string(),
            //TODO: days/hours/minutes
        ];
    }

    public static function type(): string
    {
        return 'case';
    }
}
