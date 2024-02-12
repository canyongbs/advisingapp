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

namespace AdvisingApp\InventoryManagement\Filament\Resources\AssetResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Tables\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\InventoryManagement\Models\Asset;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Filament\Tables\Columns\OpenSearch\TextColumn;
use AdvisingApp\InventoryManagement\Models\Scopes\ClassifiedAs;
use AdvisingApp\InventoryManagement\Filament\Resources\AssetResource;
use AdvisingApp\InventoryManagement\Enums\SystemAssetStatusClassification;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'available' => Tab::make('Available')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('status', fn (Builder $query) => $query->tap(new ClassifiedAs(SystemAssetStatusClassification::Available)))),
            'checked_out' => Tab::make('Checked Out')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('status', fn (Builder $query) => $query->tap(new ClassifiedAs(SystemAssetStatusClassification::CheckedOut)))),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial_number')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type.name'),
                TextColumn::make('status.name'),
                TextColumn::make('location.name'),
                TextColumn::make('purchase_age')
                    ->label('Device Age')
                    ->sortable(['purchase_date'])
                    ->tooltip(fn (Asset $record) => $record->purchase_date->format('M j, Y')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->relationship('type', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('location')
                    ->relationship('location', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->poll('60s');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
