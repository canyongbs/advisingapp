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

namespace Assist\Engagement\Filament\Resources\EngagementResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Enums\EngagementDeliveryStatus;
use App\Filament\Resources\RelationManagers\RelationManager;

class EngagementDeliverablesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliverable';

    protected static ?string $recordTitleAttribute = 'channel';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('channel')
                    ->label('How would you like to send this engagement?')
                    ->translateLabel()
                    ->options(EngagementDeliveryMethod::class)
                    ->disableOptionWhen(fn (string $value) => $this->ownerRecord->deliverable->channel === EngagementDeliveryMethod::from($value))
                    ->validationAttribute('Delivery Method')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('channel')
            ->columns([
                IdColumn::make(),
                TextColumn::make('channel'),
                IconColumn::make('delivery_status')
                    ->icon(fn (EngagementDeliveryStatus $state): string => match ($state) {
                        EngagementDeliveryStatus::Successful => 'heroicon-o-check-circle',
                        EngagementDeliveryStatus::Awaiting => 'heroicon-o-clock',
                        EngagementDeliveryStatus::Failed => 'heroicon-o-x-circle',
                    })
                    ->color(fn (EngagementDeliveryStatus $state): string => match ($state) {
                        EngagementDeliveryStatus::Successful => 'success',
                        EngagementDeliveryStatus::Awaiting => 'info',
                        EngagementDeliveryStatus::Failed => 'danger',
                    }),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
