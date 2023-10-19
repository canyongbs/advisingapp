<?php

namespace Assist\Campaign\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Builder;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Assist\Campaign\Models\CampaignAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Campaign\Filament\Blocks\EngagementBatchBlock;
use App\Filament\Resources\RelationManagers\RelationManager;

class CampaignActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    public function form(Form $form): Form
    {
        /** @var CampaignAction $action */
        $action = $form->model;

        return $form
            ->schema([
                TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                ...$action->getEditFields(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('type'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Builder::make('data')
                            ->addActionLabel('Add a new Campaign Action')
                            ->maxItems(1)
                            ->blocks([
                                EngagementBatchBlock::make(),
                            ]),
                    ])
                    ->using(function (array $data, string $model): CampaignAction {
                        return $model::create([
                            'campaign_id' => $this->getOwnerRecord()->id,
                            'type' => $data['data'][0]['type'],
                            'data' => $data['data'][0]['data'],
                        ]);
                    })
                    ->hidden(fn () => $this->getOwnerRecord()->hasBeenExecuted() === true),
            ])
            ->actions([
                EditAction::make()
                    ->hidden(fn () => $this->getOwnerRecord()->hasBeenExecuted() === true),
                DeleteAction::make()
                    ->hidden(fn () => $this->getOwnerRecord()->hasBeenExecuted() === true),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
                    ->hidden(fn () => $this->getOwnerRecord()->hasBeenExecuted() === true),
            ]);
    }
}
