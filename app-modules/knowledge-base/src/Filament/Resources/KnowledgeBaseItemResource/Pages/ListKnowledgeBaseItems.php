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

namespace AdvisingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Tables\Table;
use Filament\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use AdvisingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use AdvisingApp\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class ListKnowledgeBaseItems extends ListRecords
{
    protected ?string $heading = 'Knowledge Management';

    protected static string $resource = KnowledgeBaseItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->label('Title')
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
                TextColumn::make('upvotes_count')
                    ->label('Upvotes')
                    ->counts([
                        'upvotes',
                        'upvotes as my_upvotes_count' => fn (Builder $query) => $query->whereBelongsTo(auth()->user()),
                    ])
                    ->sortable()
                    ->action(fn (KnowledgeBaseItem $record) => $record->toggleUpvote())
                    ->color(fn (KnowledgeBaseItem $record): string => $record->my_upvotes_count ? 'success' : 'gray')
                    ->tooltip(fn (KnowledgeBaseItem $record): string => $record->my_upvotes_count ? 'Click to remove upvote' : 'Click to upvote')
                    ->formatStateUsing(fn (KnowledgeBaseItem $record, int $state): string => ($record->my_upvotes_count ? 'Upvoted ' : 'Upvote ') . "({$state})"),
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
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(fn (): bool => ! auth()->user()->can('knowledge_base_item.create'))
                ->label('Create Knowledge Base Article')
                ->createAnother(false)
                ->successRedirectUrl(fn (Model $record): string => KnowledgeBaseItemResource::getUrl('edit', ['record' => $record])),
        ];
    }
}
