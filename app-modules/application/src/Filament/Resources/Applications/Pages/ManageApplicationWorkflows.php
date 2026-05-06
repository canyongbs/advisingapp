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

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Workflow\Enums\WorkflowTriggerEvent;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Filament\Resources\Workflows\WorkflowResource;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Features\AdmissionsStageWorkflowTriggersFeature;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

class ManageApplicationWorkflows extends ManageRelatedRecords
{
    protected static string $resource = ApplicationResource::class;

    protected static string $relationship = 'workflows';

    public static function getNavigationLabel(): string
    {
        return 'Workflows';
    }

    public function getDefaultActiveTab(): string | int | null
    {
        if (! AdmissionsStageWorkflowTriggersFeature::active()) {
            return null;
        }

        // @phpstan-ignore method.notFound
        $firstState = ApplicationSubmissionState::query()
            ->withoutArchivedAndUnused()
            ->oldest('id')
            ->first();

        return $firstState
            ? $firstState->id
            : 'all';
    }

    public function getTabs(): array
    {
        if (! AdmissionsStageWorkflowTriggersFeature::active()) {
            return [];
        }

        $owner = $this->getOwnerRecord();

        $states = ApplicationSubmissionState::query()
            ->withCount([
                'workflowTriggers' => fn (Builder $query) => $query
                    ->where('related_type', $owner->getMorphClass())
                    ->where('related_id', $owner->getKey()),
            ])
            ->oldest('id')
            ->get();

        $tabs = [];

        foreach ($states as $state) {
            if (filled($state->archived_at) && $state->workflow_triggers_count === 0) {
                continue;
            }

            $label = $state->name;

            if (filled($state->archived_at)) {
                $label .= ' (Archived)';
            }

            $tabs[$state->id] = Tab::make($label)
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas(
                    'workflowTrigger',
                    fn (Builder $query) => $query
                        ->where('sub_related_type', $state->getMorphClass())
                        ->where('sub_related_id', $state->id),
                ));
        }

        $tabs['all'] = Tab::make('All');

        return $tabs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('workflowTrigger.subRelated.name')
                    ->label('Stage')
                    ->visible(fn (): bool => AdmissionsStageWorkflowTriggersFeature::active()),
                TextColumn::make('workflowTrigger.event')
                    ->label('Trigger')
                    ->badge()
                    ->visible(fn (): bool => AdmissionsStageWorkflowTriggersFeature::active()),
                IconColumn::make('is_enabled')
                    ->label('Enabled')
                    ->boolean(),
                TextColumn::make('steps')
                    ->getStateUsing(fn (Workflow $record) => $record->workflowSteps()->count()),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Workflow $record) => WorkflowResource::getUrl('edit', [$record])),
                DeleteAction::make()
                    ->modalHeading(fn (Workflow $record) => 'Delete ' . $record->name),
            ])
            ->recordUrl(fn (Workflow $record) => WorkflowResource::getUrl('edit', [$record]));
    }

    /**
     * @return array<Action>
     */
    public function getHeaderActions(): array
    {
        $action = Action::make('create')
            ->label('Create New Workflow');

        // TODO: Cleanup Task - Once AdmissionsStageWorkflowTriggersFeature is removed:
        //   - Delete the surrounding `if (...::active()) { ... }` and KEEP what's inside it
        //     (the slide-over modal with the Stage + Trigger fields is the new UX).
        //   - Inside the action callback below, drop the second `::active()` check and the
        //     ternaries — just pass $data['sub_related_id'] and $data['event'] directly
        //     to the WorkflowTrigger (sub_related_type is always 'application_submission_state'
        //     for this page).
        if (AdmissionsStageWorkflowTriggersFeature::active()) {
            $action = $action
                ->slideOver()
                ->modalHeading('Create New Workflow')
                ->schema([
                    Select::make('sub_related_id')
                        ->label('Stage')
                        ->options(
                            // @phpstan-ignore method.notFound
                            fn (): array => ApplicationSubmissionState::query()
                                ->withoutArchived()
                                ->oldest('id')
                                ->pluck('name', 'id')
                                ->all(),
                        )
                        ->default(fn (): ?string => $this->resolveDefaultStateId())
                        ->required(),
                    Radio::make('event')
                        ->label('Trigger')
                        ->options(WorkflowTriggerEvent::class)
                        ->default(WorkflowTriggerEvent::Enter->value)
                        ->required()
                        ->inline()
                        ->inlineLabel(false),
                ]);
        }

        return [
            $action->action(function (array $data): void {
                try {
                    DB::beginTransaction();

                    $workflowTrigger = new WorkflowTrigger([
                        'type' => WorkflowTriggerType::EventBased,
                        'sub_related_type' => AdmissionsStageWorkflowTriggersFeature::active()
                            ? (new ApplicationSubmissionState())->getMorphClass()
                            : null,
                        'sub_related_id' => AdmissionsStageWorkflowTriggersFeature::active()
                            ? $data['sub_related_id']
                            : null,
                        'event' => AdmissionsStageWorkflowTriggersFeature::active()
                            ? $data['event']
                            : null,
                    ]);

                    $workflowTrigger->related()->associate($this->getOwnerRecord());
                    $workflowTrigger->createdBy()->associate(auth()->user());

                    $workflowTrigger->save();

                    $workflow = new Workflow([
                        'name' => 'Application Workflow',
                        'is_enabled' => false,
                    ]);

                    $workflow->workflowTrigger()->associate($workflowTrigger);

                    $workflow->save();

                    DB::commit();
                } catch (Throwable $throw) {
                    DB::rollBack();

                    throw $throw;
                }

                redirect(WorkflowResource::getUrl('edit', [$workflow]));
            }),
        ];
    }

    private function resolveDefaultStateId(): ?string
    {
        $activeTab = $this->activeTab;

        if (is_string($activeTab) && $activeTab !== 'all') {
            return $activeTab;
        }

        // @phpstan-ignore method.notFound
        return ApplicationSubmissionState::query()
            ->withoutArchived()
            ->oldest('id')
            ->value('id');
    }
}
