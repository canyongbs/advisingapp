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

namespace AdvisingApp\Workflow\Filament\Resources\Workflows\RelationManagers;

use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementEmailDetails;
use AdvisingApp\Workflow\Models\WorkflowEngagementSmsDetails;
use AdvisingApp\Workflow\Models\WorkflowProactiveAlertDetails;
use AdvisingApp\Workflow\Models\WorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowSubscriptionDetails;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use Carbon\CarbonInterval;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Builder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class WorkflowStepsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowSteps';

    protected static ?string $title = 'Workflow Steps';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->state(function (WorkflowStep $record) {
                        assert($record->currentDetails instanceof WorkflowDetails);

                        return $record->currentDetails->getLabel();
                    }),
                TextColumn::make('delay_minutes')
                    ->label('Delay from Previous Step')
                    ->state(fn(WorkflowStep $record) => CarbonInterval::minutes($record->delay_minutes)->cascade()->forHumans()),
            ])
            ->headerActions([
                Action::make('create')
                    ->label('New Step')
                    ->modalHeading('Create Workflow Steps')
                    ->schema([
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
                    ->action(fn() => null),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(function (WorkflowStep $workflowStep) {
                        assert($workflowStep->currentDetails instanceof WorkflowDetails);

                        return 'Edit ' . Str::title($workflowStep->currentDetails->getLabel());
                    })
                    ->fillForm(function (WorkflowStep $record): array {
                        assert($record->currentDetails instanceof WorkflowDetails);

                        $block = $record->currentDetails->getBlock();
                        $block->prepareForEdit($record->currentDetails);

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

                        $block = $record->currentDetails->getBlock();
                        $transformedData = $block->beforeUpdate($data);

                        $record->currentDetails->update($transformedData);

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
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @param WorkflowActionBlock $block
     * @param array<string, mixed> $data
     *
     * @return WorkflowDetails|null
     */
    private function createWorkflowDetails(WorkflowActionBlock $block, array $data): ?WorkflowDetails
    {
        $transformedData = $block->beforeCreate($data);

        $action = match ($block->type()) {
            'workflow_case_details' => WorkflowCaseDetails::create([
                'division_id' => $transformedData['division_id'],
                'status_id' => $transformedData['status_id'],
                'priority_id' => $transformedData['priority_id'],
                'assigned_to_id' => $transformedData['assigned_to_id'],
                'close_details' => $transformedData['close_details'],
                'res_details' => $transformedData['res_details'],
            ]),
            'workflow_engagement_email_details' => WorkflowEngagementEmailDetails::create([
                'channel' => $transformedData['channel'],
                'subject' => $transformedData['subject'],
                'body' => $transformedData['body'],
            ]),
            'workflow_engagement_sms_details' => WorkflowEngagementSmsDetails::create([
                'channel' => $transformedData['channel'],
                'body' => $transformedData['body'],
            ]),
            'workflow_proactive_alert_details' => WorkflowProactiveAlertDetails::create([
                'description' => $transformedData['description'],
                'severity' => $transformedData['severity'],
                'suggested_intervention' => $transformedData['suggested_intervention'],
                'status_id' => $transformedData['status_id'],
            ]),
            'workflow_task_details' => WorkflowTaskDetails::create([
                'title' => $transformedData['title'],
                'description' => $transformedData['description'],
                'due' => $transformedData['due'],
                'assigned_to' => $transformedData['assigned_to'],
            ]),
            'workflow_subscription_block' => WorkflowSubscriptionDetails::create([
                'user_ids' => $transformedData['user_ids'],
                'remove_prior' => $transformedData['remove_prior'],
            ]),
            default => null,
        };

        return $action;
    }
}
