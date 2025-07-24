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

use AdvisingApp\Workflow\Enums\WorkflowActionType;
use AdvisingApp\Workflow\Filament\Blocks\WorkflowActionBlock;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowDetails;
use AdvisingApp\Workflow\Models\WorkflowStep;
use Carbon\CarbonInterval;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
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

class WorkflowActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'workflowSteps';

    protected static ?string $title = 'Workflow Steps';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Group::make(fn (WorkflowStep $record) => $record->current_details_type->getEditFields()),
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
                TextColumn::make('current_details_type')
                    ->label('Step Type'),
                TextColumn::make('delay_minutes')
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
                            ->blocks(WorkflowActionType::blocks())
                            ->dehydrated(false)
                            ->model($workflow)
                            ->saveRelationshipsUsing(function (Builder $component, Workflow $record) {
                                foreach ($component->getChildComponentContainers() as $item) {
                                    $block = $item->getParentComponent();

                                    assert($block instanceof WorkflowActionBlock);

                                    $data = $item->getState(false);

                                    try {
                                        DB::beginTransaction();

                                        $action = $this->createWorkflowDetails($block, $data, $record);

                                        $block->afterCreated($action, $item);

                                        $item->model($action)->saveRelationships();

                                        DB::commit();
                                    } catch (Throwable $throw) {
                                        DB::rollBack();

                                        throw $throw;
                                    }
                                }
                            }),
                    ])
                    ->action(fn () => null)
                    ->hidden(function () use ($workflow) {
                        return $workflow->hasBeenExecuted();
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn (WorkflowStep $workflowStep) => 'Edit ' . Str::title($workflowStep->current_details_type->getLabel()))
                    ->hidden(function (WorkflowStep $workflowStep) {
                        $details = $workflowStep->currentDetails;

                        assert($details instanceof WorkflowDetails);

                        return $details->hasBeenExecuted();
                    })
                    ->databaseTransaction(),
                DeleteAction::make()
                    ->modalHeading(fn (WorkflowStep $workflowStep) => 'Delete ' . Str::title($workflowStep->current_details_type->getLabel()))
                    ->hidden(function (WorkflowStep $workflowStep) {
                        $details = $workflowStep->currentDetails;

                        assert($details instanceof WorkflowDetails);

                        return $details->hasBeenExecuted();
                    })
                    ->databaseTransaction(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
                    ->hidden(fn () => $workflow->hasBeenExecuted()),
            ]);
    }

    /**
     * @param WorkflowActionBlock $block
     * @param array<string> $data
     * @param Workflow $workflow
     *
     * @return WorkflowDetails
     */
    private function createWorkflowDetails(WorkflowActionBlock $block, array $data, Workflow $workflow): WorkflowDetails
    {
        // $action = match ($block->type()) {
        //     'workflow_case_details' => WorkflowCaseDetails::create([
        //         'division_id' => $data['division_id'],
        //         'status_id' => $data['status_id'],
        //         'priority_id' => $data['priority_id'],
        //         'assigned_to_id' => $data['assigned_to_id'],
        //         'close_details' => $data['close_details'],
        //         'res_details' => $data['res_details'],
        //     ]),
        //     default => null
        // };

        $action = WorkflowCaseDetails::create([
                'division_id' => $data['division_id'],
                'status_id' => $data['status_id'],
                'priority_id' => $data['priority_id'],
                'assigned_to_id' => $data['assigned_to_id'],
                'close_details' => $data['close_details'],
                'res_details' => $data['res_details'],
        ]);

        $delayMinutes = ((int)$data['days'] * 24 * 60) + ((int)$data['hours'] * 60) + (int)$data['minutes'];

        $workflowStep = new WorkflowStep([
            'delay_minutes' => $delayMinutes,
            'workflow_id' => $workflow->getKey(),
            'current_details_type' => $action->getType(),
            'current_details_id' => $action->getKey(),
        ]);

        $workflowStep->save();

        $workflow->load('workflowSteps');

        return $action;
    }
}
