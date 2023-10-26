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
use Assist\Campaign\Enums\CampaignActionType;
use Assist\Engagement\Models\EngagementBatch;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\ServiceManagement\Models\ServiceRequest;
use App\Filament\Resources\RelationManagers\RelationManager;
use Assist\Campaign\Filament\Resources\CampaignResource\Pages\CreateCampaign;

class CampaignActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    public function form(Form $form): Form
    {
        /** @var CampaignAction $action */
        $action = $form->model;

        $form->model = match ($action->type) {
            CampaignActionType::BulkEngagement => EngagementBatch::class,
            CampaignActionType::ServiceRequest => ServiceRequest::class
        };

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
                            ->blocks(CreateCampaign::blocks()),
                    ])
                    ->using(function (array $data, string $model): CampaignAction {
                        foreach ($data['data'] as $action) {
                            $lastModel = $model::create([
                                'campaign_id' => $this->getOwnerRecord()->id,
                                'type' => $action['type'],
                                'data' => $action['data'],
                            ]);
                        }

                        return $lastModel;
                    })
                    ->hidden(fn () => $this->getOwnerRecord()->hasBeenExecuted() === true),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(fn (CampaignAction $action) => 'Edit ' . $action->type->getLabel())
                    ->hidden(fn () => $this->getOwnerRecord()->hasBeenExecuted() === true),
                DeleteAction::make()
                    ->modalHeading(fn (CampaignAction $action) => 'Delete ' . $action->type->getLabel())
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
