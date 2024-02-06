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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\DateTimePicker;
use App\Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\InventoryManagement\Models\MaintenanceProvider;
use AdvisingApp\InventoryManagement\Enums\MaintenanceActivityStatus;

class MaintenanceActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'maintenanceActivities';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('details')
                ->label('Nature of Service')
                ->required(),
            Select::make('maintenance_provider_id')
                ->relationship('maintenanceProvider', 'name')
                ->preload()
                ->label('Provider')
                ->exists((new MaintenanceProvider())->getTable(), 'id'),
            Select::make('status')
                ->label('Status')
                ->reactive()
                ->searchable()
                ->options(MaintenanceActivityStatus::class)
                ->required()
                ->enum(MaintenanceActivityStatus::class),
            DateTimePicker::make('completed_date')
                ->label('Completed On')
                ->visible(fn (callable $get) => $get('status') === MaintenanceActivityStatus::Completed->value)
                ->requiredIf('status', MaintenanceActivityStatus::Completed->value),
            DateTimePicker::make('scheduled_date')
                ->label('Scheduled For')
                ->visible(fn (callable $get) => $get('status') !== MaintenanceActivityStatus::Completed->value)
                ->requiredIf('status', [MaintenanceActivityStatus::Scheduled->value, MaintenanceActivityStatus::Delayed->value]),
            Textarea::make('notes')
                ->label('Notes'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Maintenance Activity')
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('details'),
                TextColumn::make('maintenanceProvider.name'),
                TextColumn::make('status'),
                TextColumn::make('scheduled_date')
                    ->label('Scheduled For'),
                TextColumn::make('completed_date')
                    ->label('Completed On'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(MaintenanceActivityStatus::class)
                    ->label('Status'),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
            ])
            ->defaultSort('scheduled_date', 'asc');
    }
}
