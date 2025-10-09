<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages;

use AdvisingApp\Campaign\Filament\Resources\Campaigns\CampaignResource;
use AdvisingApp\Campaign\Models\Campaign;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListCampaigns extends ListRecords
{
    protected static string $resource = CampaignResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
                TextColumn::make('segment.name')
                    ->label('Segment'),
                TextColumn::make('enabled')
                    ->label('Enabled')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'blue' : 'orange'),
                TextColumn::make('execution_status')
                    ->label('Completed')
                    ->state(fn (Campaign $record) => $record->hasBeenExecuted())
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Yes' : 'No')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'blue' : 'orange'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->searchable()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->hidden(fn (Campaign $record) => $record->hasBeenExecuted() === true),
                DeleteAction::make()
                    ->hidden(fn (Campaign $record) => $record->hasBeenExecuted() === true),
            ])->filters([
                Filter::make('My Campaigns')
                    ->query(
                        fn (Builder $query) => $query
                            ->where('created_by_id', auth()->id())
                            ->where('created_by_type', (new User())->getMorphClass()),
                    ),

                TernaryFilter::make('Enabled')
                    ->placeholder('-')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled')
                    ->queries(
                        true: fn (Builder $query) => $query->where('enabled', true),
                        false: fn (Builder $query) => $query->where('enabled', false),
                        blank: fn (Builder $query) => $query
                    ),

                TernaryFilter::make('Completed')
                    ->placeholder('-')
                    ->trueLabel('Completed')
                    ->falseLabel('In Progress')
                    ->queries(
                        true: function (Builder $query): Builder {
                            return $query->whereDoesntHave('actions', function (Builder $query) {
                                $query->whereNull('execution_finished_at');
                            });
                        },
                        false: function (Builder $query): Builder {
                            return $query->whereHas('actions', function (Builder $query) {
                                $query->whereNull('execution_finished_at');
                            });
                        },
                        blank: fn (Builder $query) => $query
                    ),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
