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

namespace AdvisingApp\Workflow\Filament\Blocks;

use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Division\Models\Division;
use App\Models\User;
use Closure;
use Exception;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
     * @return array<int, covariant Field | Section>
     */
    public function generateFields(): array
    {
        return [
            Select::make('division_id')
                ->relationship('division', 'name')
                ->model(CaseModel::class)
                ->default(fn () => auth()->user()->team?->division?->getKey()
                    ?? Division::query()->where('is_default', true)->first()?->getKey()
                    ?? Division::query()->first()?->getKey()
                    ?? new Exception('No division found'))
                ->label('Division')
                ->hidden(fn () => Division::count() === 1 || Division::where('is_default', true)->exists())
                ->required()
                ->exists((new Division())->getTable(), 'id')
                ->dehydratedWhenHidden(),
            Select::make('status_id')
                ->relationship('status', 'name')
                ->model(CaseModel::class)
                ->preload()
                ->label('Status')
                ->required()
                ->exists((new CaseStatus())->getTable(), 'id'),
            Select::make('type_id')
                ->options(CaseType::pluck('name', 'id'))
                ->afterStateUpdated(function (Set $set, Get $get) {
                    $set('priority_id', null);
                    $set('assigned_to_id', null);
                })
                ->afterStateHydrated(function (Set $set, Get $get, mixed $state): void {
                    if (! $state && filled($get('priority_id'))) {
                        $priority = CasePriority::find($get('priority_id'));

                        if ($priority && $priority->type_id) {
                            $set('type_id', $priority->type_id);
                        }
                    }
                })
                ->label('Type')
                ->required()
                ->live()
                ->exists(CaseType::class, 'id')
                ->dehydrated(false),
            Select::make('priority_id')
                ->options(function (Get $get) {
                    $typeId = $get('type_id');

                    if (! $typeId) {
                        $priorityId = $get('priority_id');

                        if ($priorityId) {
                            $priority = CasePriority::find($priorityId);

                            if ($priority) {
                                $typeId = $priority->type_id;
                            }
                        }

                        if (! $typeId) {
                            return [];
                        }
                    }

                    return CasePriority::query()
                        ->where('type_id', $typeId)
                        ->orderBy('order')
                        ->pluck('name', 'id');
                })
                ->label('Priority')
                ->required()
                ->exists((new CasePriority())->getTable(), 'id')
                ->visible(fn (Get $get) => filled($get('type_id')) || filled($get('priority_id')))
                ->live(),
            Select::make('assigned_to_id')
                ->label('Assign Case to')
                ->options(function (Get $get) {
                    $caseTypeId = $get('type_id');

                    if (! $caseTypeId && filled($get('priority_id'))) {
                        $priority = CasePriority::find($get('priority_id'));

                        if ($priority) {
                            $caseTypeId = $priority->type_id;
                        }
                    }

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
            Section::make('How long after the previous step should this occur?')
                ->schema([
                    TextInput::make('days')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                    TextInput::make('hours')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                    TextInput::make('minutes')
                        ->translateLabel()
                        ->numeric()
                        ->step(1)
                        ->minValue(0)
                        ->default(0)
                        ->inlineLabel(),
                ])
                ->columns(3),
        ];
    }

    /**
     * @return array<int, covariant Field | Section>
     */
    public function editFields(): array
    {
        return $this->generateFields();
    }

    public static function type(): string
    {
        return 'workflow_case_details';
    }
}
