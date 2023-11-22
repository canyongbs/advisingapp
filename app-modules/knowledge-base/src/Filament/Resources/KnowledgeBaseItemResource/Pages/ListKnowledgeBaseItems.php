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

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ListKnowledgeBaseItems extends ListRecords
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('question')
                    ->label('Question/Issue/Feature')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quality.name')
                    ->label('Quality')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('public')
                    ->label('Public')
                    ->translateLabel()
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->translateLabel()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('quality')
                    ->relationship('quality', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('public'),
            ])
            ->actions([
                ViewAction::make(),
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
