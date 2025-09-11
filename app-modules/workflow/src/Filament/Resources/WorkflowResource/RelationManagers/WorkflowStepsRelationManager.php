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

namespace AdvisingApp\Workflow\Filament\Resources\WorkflowResource\RelationManagers;

use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
use AdvisingApp\Workflow\Models\WorkflowProactiveAlertDetails;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use Carbon\CarbonInterval;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class WorkflowStepsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowSteps';

    protected static ?string $title = 'Workflow Steps';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make(function (WorkflowStep $record) {
                    assert($record->currentDetails instanceof WorkflowDetails);

                    return $record->currentDetails->getEditFields();
                }),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        $workflow = $this->getOwnerRecord();

        assert($workflow instanceof Workflow);

        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('type')
                    ->label('Step Type')
                    ->getStateUsing(function (WorkflowStep $record) {
                        assert($record->currentDetails instanceof WorkflowDetails);

                        return $record->currentDetails->getLabel();
                    }),
                TextColumn::make('delay_minutes')
                    ->label('Delay from Previous Step')
                    ->state(fn (WorkflowStep $record) => CarbonInterval::minutes($record->delay_minutes)->cascade()->forHumans()),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('New Step')
                    ->modalHeading('Create Workflow Steps')
                    ->form([
                        Builder::make('data')
                            ->hiddenLabel()
                            ->addActionLabel('Add a New Workflow Step')
                            ->blocks(WorkflowDetails::blocks())
                            ->dehydrated(false)
                            ->model($workflow)
                            ->saveRelationshipsUsing(function (Builder $component, Workflow $record) {
                                $previousStepId = null;

                                foreach ($component->getChildComponentContainers() as $item) {
                                    $block = $item->getParentComponent();

                                    assert($block instanceof WorkflowActionBlock);

                                    $data = $item->getState(false);

                                    try {
                                        DB::beginTransaction();

                                        $action = $this->createWorkflowDetails($block, $data);

                                        $delayMinutes = ($data['days'] * 24 * 60) + ($data['hours'] * 60) + $data['minutes'];

                                        $workflowStep = new WorkflowStep([
                                            'delay_minutes' => $delayMinutes,
                                        ]);

                                        $workflowStep->workflow()->associate($record);
                                        $workflowStep->currentDetails()->associate($action);
                                        $workflowStep->previousWorkflowStep()->associate($previousStepId);

                                        $workflowStep->save();

                                        $record->load('workflowSteps');

                                        $block->afterCreated($action, $item);

                                        $item->model($action)->saveRelationships();

                                        DB::commit();
                                    } catch (Throwable $throw) {
                                        DB::rollBack();

                                        throw $throw;
                                    }

                                    $previousStepId = $workflowStep->getKey();
                                }
                            }),
                    ])
                    ->action(fn () => null),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(function (WorkflowStep $workflowStep) {
                        assert($workflowStep->currentDetails instanceof WorkflowDetails);

                        return 'Edit ' . Str::title($workflowStep->currentDetails->getLabel());
                    })
                    ->fillForm(function (WorkflowStep $record): array {
                        assert($record->currentDetails instanceof WorkflowDetails);

                        if ($record->currentDetails instanceof WorkflowCaseDetails) {
                            $record->currentDetails->load('priority.type');
                        }

                        $data = $record->currentDetails->toArray();

                        $totalMinutes = $record->delay_minutes;
                        $data['days'] = intval($totalMinutes / (24 * 60));
                        $totalMinutes %= (24 * 60);
                        $data['hours'] = intval($totalMinutes / 60);
                        $data['minutes'] = $totalMinutes % 60;

                        return $data;
                    })
                    ->using(function (array $data, WorkflowStep $record): WorkflowStep {
                        assert($record->currentDetails instanceof WorkflowDetails);

                        $delayMinutes = ($data['days'] * 24 * 60) + ($data['hours'] * 60) + $data['minutes'];
                        $record->delay_minutes = $delayMinutes;
                        $record->save();

                        unset($data['days'], $data['hours'], $data['minutes']);

                        if ($record->currentDetails instanceof WorkflowCaseDetails) {
                            if (isset($data['assigned_to_id']) && $data['assigned_to_id'] === 'automatic') {
                                $data['assigned_to_id'] = null;
                            }
                        }

                        $record->currentDetails->update($data);

                        return $record;
                    })
                    ->databaseTransaction(),
                DeleteAction::make()
                    ->modalHeading(function (WorkflowStep $workflowStep) {
                        assert($workflowStep->currentDetails instanceof WorkflowDetails);

                        return 'Delete ' . Str::title($workflowStep->currentDetails->getLabel());
                    })
                    ->databaseTransaction(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @param WorkflowActionBlock $block
     * @param array<string> $data
     *
     * @return WorkflowDetails|null
     */
    private function createWorkflowDetails(WorkflowActionBlock $block, array $data): ?WorkflowDetails
    {
        $action = match ($block->type()) {
            'workflow_case_details' => WorkflowCaseDetails::create([
                'division_id' => $data['division_id'],
                'status_id' => $data['status_id'],
                'priority_id' => $data['priority_id'],
                'assigned_to_id' => $data['assigned_to_id'] === 'automatic' ? null : $data['assigned_to_id'],
                'close_details' => $data['close_details'],
                'res_details' => $data['res_details'],
            ]),
            'workflow_engagement_email_details' => WorkflowEngagementEmailDetails::create([
                'channel' => $data['channel'],
                'subject' => $data['subject'],
                'body' => $data['body'],
            ]),
            'workflow_engagement_sms_details' => WorkflowEngagementSmsDetails::create([
                'channel' => $data['channel'],
                'body' => $data['body'],
            ]),
            'workflow_proactive_alert_details' => WorkflowProactiveAlertDetails::create([
                'description' => $data['description'],
                'severity' => $data['severity'],
                'suggested_intervention' => $data['suggested_intervention'],
                'status_id' => $data['status_id'],
            ]),
            'workflow_task_details' => WorkflowTaskDetails::create([
                'title' => $data['title'],
                'description' => $data['description'],
                'due' => $data['due'],
                'assigned_to' => $data['assigned_to'],
            ]),
            default => null
        };

        return $action;
    }
}
