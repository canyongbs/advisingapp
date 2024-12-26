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

namespace AdvisingApp\Ai\Filament\Resources\PromptResource\Pages;

use AdvisingApp\Ai\Filament\Resources\PromptResource;
use AdvisingApp\Ai\Filament\Resources\PromptTypeResource;
use AdvisingApp\Ai\Models\Prompt;
use App\Features\SmartPromptsFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListPrompts extends ListRecords
{
    protected static string $resource = PromptResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('type.title')
                    ->sortable()
                    ->url(fn (Prompt $record) => PromptTypeResource::getUrl('view', ['record' => $record->type])),
                TextColumn::make('is_smart')
                    ->label('Kind')
                    ->state(fn (Prompt $record): string => $record->is_smart ? 'Smart' : 'Custom')
                    ->visible(SmartPromptsFeature::active()),
                TextColumn::make('uses_count')
                    ->label('Uses')
                    ->counts('uses')
                    ->sortable(),
                TextColumn::make('upvotes_count')
                    ->label('Upvotes')
                    ->counts([
                        'upvotes',
                        'upvotes as my_upvotes_count' => fn (Builder $query) => $query->whereBelongsTo(auth()->user()),
                    ])
                    ->sortable()
                    ->action(fn (Prompt $record) => $record->toggleUpvote())
                    ->color(fn (Prompt $record): string => $record->my_upvotes_count ? 'success' : 'gray')
                    ->tooltip(fn (Prompt $record): string => $record->my_upvotes_count ? 'Click to remove upvote' : 'Click to upvote')
                    ->formatStateUsing(fn (Prompt $record, int $state): string => ($record->my_upvotes_count ? 'Upvoted ' : 'Upvote ') . "({$state})"),
            ])
            ->filters([
                TernaryFilter::make('is_smart')
                    ->label('Kind')
                    ->trueLabel('Smart')
                    ->falseLabel('Custom'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(auth()->user()->isSuperAdmin()),
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
