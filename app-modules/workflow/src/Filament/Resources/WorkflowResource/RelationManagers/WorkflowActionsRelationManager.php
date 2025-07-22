<?php

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
