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
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowStep;
use Filament\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

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
            //->modifyQueryUsing(fn (Builder $query) => $query->orderBy(you know))
            ->columns([
                //???? what Model even Is this
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
                            ->model($this->getOwnerRecord())
                            ->saveRelationshipsUsing(),
                    ])
                    ->action(fn() => null)
                    ->hidden(fn() => $workflow->)
            ])
    }
}
