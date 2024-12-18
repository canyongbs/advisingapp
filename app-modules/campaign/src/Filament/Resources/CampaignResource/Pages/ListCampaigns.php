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

namespace AdvisingApp\Campaign\Filament\Resources\CampaignResource\Pages;

use AdvisingApp\Campaign\Filament\Resources\CampaignResource;
use AdvisingApp\Campaign\Models\Campaign;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
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
                    ->label('Population Segment'),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('execution_status')
                    ->label('Complete')
                    ->getStateUsing(fn (Campaign $record) => $record->hasBeenExecuted())
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
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
                Filter::make('Enabled')
                    ->query(fn (Builder $query) => $query->where('enabled', true)),
                Filter::make('Completed')
                    ->query(function (Builder $query) {
                        $query->whereDoesntHave('actions', function (Builder $query) {
                            $query->whereNull('successfully_executed_at');
                        });
                    }),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
