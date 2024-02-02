<?php

/*
<COPYRIGHT>

    Copyright © 2022-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Filters\OpenSearch\SelectFilter;
use AdvisingApp\Analytics\Filament\Resources\AnalyticsResourceResource;

class ListAnalyticsResources extends ListRecords
{
    protected static string $resource = AnalyticsResourceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(60),
                TextColumn::make('url')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('source.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->sortable(),
                IconColumn::make('is_included_in_data_portal')
                    ->boolean()
                    ->label('Included in Data Portal')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),
                TernaryFilter::make('is_included_in_data_portal')
                    ->label('Included in Data Portal'),
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->preload()
                    ->multiple(),
                SelectFilter::make('source')
                    ->relationship('source', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
