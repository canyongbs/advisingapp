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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Settings\CampaignSettings;
use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\CaseManagement\Models\CaseType;
use AdvisingApp\Division\Models\Division;
use App\Models\User;
use Carbon\CarbonImmutable;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;

class CaseBlock extends CampaignActionBlock
{
    /**
     * @var Model | array<string, mixed> | class-string<Model> | Closure | null
     */
    protected Model | array | string | Closure | null $model = CaseModel::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Case');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Select::make($fieldPrefix . 'division_id')
                ->relationship('division', 'name')
                ->model(CaseModel::class)
                ->default(
                    fn () => auth()->user()->team?->division?->getKey()
                        ?? Division::query()
                            ->where('is_default', true)
                            ->first()
                            ?->getKey()
                )
                ->label('Division')
                ->visible(function () {
                    $divisionCount = Division::count();
                    $hasDefault = Division::where('is_default', true)->exists();

                    return $divisionCount > 1 && ! $hasDefault;
                })
                ->required()
                ->exists((new Division())->getTable(), 'id'),
            Select::make($fieldPrefix . 'status_id')
                ->relationship('status', 'name')
                ->model(CaseModel::class)
                ->preload()
                ->label('Status')
                ->required()
                ->exists((new CaseStatus())->getTable(), 'id'),
            Select::make($fieldPrefix . 'type_id')
                ->options(CaseType::pluck('name', 'id'))
                ->afterStateUpdated(function (Set $set) {
                    $set('priority_id', null);
                    $set('assigned_to_id', null);
                })
                ->label('Type')
                ->required()
                ->live()
                ->exists(CaseType::class, 'id'),
            Select::make($fieldPrefix . 'priority_id')
                ->options(
                    fn (Get $get) => CasePriority::query()
                        ->where('type_id', $get('type_id'))
                        ->orderBy('order')
                        ->pluck('name', 'id')
                )
                ->label('Priority')
                ->required()
                ->exists((new CasePriority())->getTable(), 'id')
                ->visible(fn (Get $get): bool => filled($get('type_id'))),
            Select::make($fieldPrefix . 'assigned_to_id')
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
            Textarea::make($fieldPrefix . 'close_details')
                ->label('Close Details/Description')
                ->nullable()
                ->string(),
            Textarea::make($fieldPrefix . 'res_details')
                ->label('Internal Case Details')
                ->nullable()
                ->string(),
            DateTimePicker::make('execute_at')
                ->label('When should the journey step be executed?')
                ->columnSpanFull()
                ->timezone(app(CampaignSettings::class)->getActionExecutionTimezone())
                ->hintIconTooltip('This time is set in ' . app(CampaignSettings::class)->getActionExecutionTimezoneLabel() . '.')
                ->lazy()
                ->helperText(fn ($state): ?string => filled($state) ? $this->generateUserTimezoneHint(CarbonImmutable::parse($state)) : null)
                ->required()
                ->minDate(now()),
        ];
    }

    public static function type(): string
    {
        return 'case';
    }
}
