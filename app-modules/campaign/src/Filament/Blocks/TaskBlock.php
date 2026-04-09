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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Forms\Components\CampaignDateTimeInput;
use AdvisingApp\Project\Models\Project;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Closure;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\Auth;

class TaskBlock extends CampaignActionBlock
{
    /**
     * @var Model | array<string, mixed> | class-string<Model> | Closure | null
     */
    protected Model | array | string | Closure | null $model = Task::class;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model(Task::class);

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Fieldset::make('Details')
                ->schema([
                    Fieldset::make('Confidentiality')
                        ->schema([
                            Checkbox::make($fieldPrefix . 'is_confidential')
                                ->label('Confidential')
                                ->live(),
                            Select::make($fieldPrefix . 'confidential_task_projects')
                                ->options(fn () => Project::query()
                                    ->orderBy('name')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $search): array => Project::query()
                                    ->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all())
                                ->getOptionLabelsUsing(
                                    fn (array $values): array => Project::query()
                                        ->whereKey($values)
                                        ->pluck('name', 'id')
                                        ->all(),
                                )
                                ->label('Projects')
                                ->multiple()
                                ->dehydrated(true)
                                ->exists('projects', 'id')
                                ->visible(fn (Get $get) => $get($fieldPrefix . 'is_confidential')),
                            Select::make($fieldPrefix . 'confidential_task_users')
                                ->options(fn () => User::query()
                                    ->orderBy('name')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $search): array => User::query()
                                    ->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all())
                                ->getOptionLabelUsing(
                                    fn (array $values): array => User::query()
                                        ->whereKey($values)
                                        ->pluck('name', 'id')
                                        ->all(),
                                )

                                ->label('Users')
                                ->multiple()
                                ->dehydrated(true)
                                ->exists('users', 'id')
                                ->visible(fn (Get $get) => $get($fieldPrefix . 'is_confidential')),
                            Select::make($fieldPrefix . 'confidential_task_teams')
                                ->options(fn () => Team::query()
                                    ->orderBy('name')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->getSearchResultsUsing(fn (string $search): array => Team::query()
                                    ->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all())
                                ->getOptionLabelUsing(
                                    fn (array $values): array => Team::query()
                                        ->whereKey($values)
                                        ->pluck('name', 'id')
                                        ->all(),
                                )
                                ->label('Teams')
                                ->multiple()
                                ->dehydrated(true)
                                ->exists('teams', 'id')
                                ->visible(fn (Get $get) => $get($fieldPrefix . 'is_confidential')),
                        ]),
                    TextInput::make($fieldPrefix . 'title')
                        ->required()
                        ->maxLength(100)
                        ->string(),
                    Textarea::make($fieldPrefix . 'description')
                        ->required()
                        ->string(),
                    DateTimePicker::make($fieldPrefix . 'due')
                        ->label('Due Date'),
                    Select::make($fieldPrefix . 'assigned_to')
                        ->label('Assigned To')
                        ->relationship('assignedTo', 'name')
                        ->model(Task::class)
                        ->nullable()
                        ->searchable()
                        ->default(Auth::id()),
                ]),
            CampaignDateTimeInput::make(),
        ];
    }

    public static function type(): string
    {
        return 'task';
    }
}
