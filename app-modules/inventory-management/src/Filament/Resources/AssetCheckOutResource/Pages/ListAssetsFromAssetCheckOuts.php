<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use AdvisingApp\InventoryManagement\Models\AssetCheckOut;
use AdvisingApp\InventoryManagement\Enums\AssetCheckOutStatus;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetCheckOutResource;

class ListAssetsFromAssetCheckOuts extends ListRecords
{
    protected static string $resource = AssetCheckOutResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('asset.name')
                    ->url(fn ($record) => AssetResource::getUrl('view', ['record' => $record->asset]))
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('asset.type.name')
                    ->searchable(),
                TextColumn::make('checked_out_at')
                    ->dateTime('g:ia - M j, Y'),
                TextColumn::make('expected_check_in_at')
                    ->dateTime('g:ia - M j, Y'),
                TextColumn::make('checkIn.checked_in_at')
                    ->label('Checked in at')
                    ->dateTime('g:ia - M j, Y'),
                TextColumn::make('asset_check_in_id')
                    ->label('Status')
                    ->state(fn (AssetCheckOut $record): AssetCheckOutStatus => $record->status)
                    ->formatStateUsing(fn (AssetCheckOutStatus $state) => $state->getLabel())
                    ->badge()
                    ->color(fn (AssetCheckOutStatus $state): string => match ($state) {
                        AssetCheckOutStatus::Returned => 'success',
                        AssetCheckOutStatus::InGoodStanding => 'info',
                        default => 'danger',
                    }),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('View Asset'),
            ])
            ->emptyStateHeading('There is no asset history to show.')
            ->defaultSort('checked_out_at', 'desc');
    }
}
