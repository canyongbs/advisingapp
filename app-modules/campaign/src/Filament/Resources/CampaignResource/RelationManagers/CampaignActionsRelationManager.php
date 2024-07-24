<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Filament\Resources\CampaignResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Builder;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use AdvisingApp\Campaign\Settings\CampaignSettings;
use Filament\Resources\RelationManagers\RelationManager;

class CampaignActionsRelationManager extends RelationManager
{
    protected static string $relationship = 'actions';

    public function form(Form $form): Form
    {
        /** @var CampaignAction $action */
        $action = $form->model;

        $form->model = $action->type->getModel();

        return $form
            ->schema([
                TextInput::make('type')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                Group::make($action->type->getEditFields())
                    ->statePath('data'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('type'),
                TextColumn::make('execute_at')
                    ->dateTime(timezone: app(CampaignSettings::class)->getActionExecutionTimezone()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->form([
                        Builder::make('data')
                            ->addActionLabel('Add a new Campaign Action')
                            ->blocks(CampaignActionType::blocks()),
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
