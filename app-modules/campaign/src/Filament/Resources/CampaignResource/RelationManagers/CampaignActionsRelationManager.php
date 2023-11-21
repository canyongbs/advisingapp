<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Campaign\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Assist\Task\Models\Task;
use Assist\Alert\Models\Alert;
use Assist\CareTeam\Models\CareTeam;
use Filament\Forms\Components\Builder;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Assist\Campaign\Models\CampaignAction;
use Assist\Interaction\Models\Interaction;
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
            CampaignActionType::ServiceRequest => ServiceRequest::class,
            CampaignActionType::ProactiveAlert => Alert::class,
            CampaignActionType::Interaction => Interaction::class,
            CampaignActionType::CareTeam => CareTeam::class,
            CampaignActionType::Task => Task::class,
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
                            $executeAt = $action['data']['execute_at'];
                            unset($action['data']['execute_at']);

                            $lastModel = $model::create([
                                'campaign_id' => $this->getOwnerRecord()->id,
                                'type' => $action['type'],
                                'data' => $action['data'],
                                'execute_at' => $executeAt,
                            ]);
                        }

                        return $lastModel ?? new CampaignAction();
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
