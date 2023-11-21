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

namespace App\Filament\Widgets;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class RecentKnowledgeBaseArticlesList extends BaseWidget
{
    protected int | string | array $columnSpan = [
        'sm' => 1,
        'md' => 1,
        'lg' => 2,
    ];

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest KB Articles (5)')
            ->query(
                KnowledgeBaseItem::latest()->limit(5)
            )
            ->columns([
                IdColumn::make(),
                TextColumn::make('question')
                    ->label('Question/Issue/Feature')
                    ->searchable()
                    ->sortable()
                    ->limit()
                    ->tooltip(fn (KnowledgeBaseItem $record): string => $record->question),
                TextColumn::make('quality.name')
                    ->label('Quality')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status.name')
                    ->label('Status')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('public')
                    ->label('Public')
                    ->sortable()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (KnowledgeBaseItem $record): string => KnowledgeBaseItemResource::getUrl(name: 'view', parameters: ['record' => $record])),
            ])
            ->recordUrl(
                fn (KnowledgeBaseItem $record): string => KnowledgeBaseItemResource::getUrl(name: 'view', parameters: ['record' => $record]),
            )
            ->paginated(false);
    }
}
