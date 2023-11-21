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

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\CareTeam\Filament\Actions\ToggleCareTeamBulkAction;
use Assist\Notifications\Filament\Actions\SubscribeBulkAction;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;
use Assist\Notifications\Filament\Actions\SubscribeTableAction;
use Assist\Engagement\Filament\Actions\Contracts\HasBulkEngagementAction;
use Assist\Engagement\Filament\Actions\Concerns\ImplementsHasBulkEngagementAction;

class ListStudents extends ListRecords implements HasBulkEngagementAction
{
    use ImplementsHasBulkEngagementAction;

    protected static string $resource = StudentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Student::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('mobile')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('sisid')
                    ->searchable(),
                TextColumn::make('otherid')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('my_caseloads')
                    ->label('My Caseloads')
                    ->options(
                        auth()->user()->caseloads()
                            ->where('model', CaseloadModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                SelectFilter::make('all_caseloads')
                    ->label('All Caseloads')
                    ->options(
                        Caseload::all()
                            ->where('model', CaseloadModel::Student)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                Filter::make('subscribed')
                    ->query(fn (Builder $query): Builder => $query->whereRelation('subscriptions.user', 'id', auth()->id())),
                TernaryFilter::make('sap')
                    ->label('SAP'),
                TernaryFilter::make('dual'),
                TernaryFilter::make('ferpa')
                    ->label('FERPA'),
                Filter::make('holds')
                    ->form([
                        TextInput::make('hold'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['hold'],
                                fn (Builder $query, $hold): Builder => $query->where('holds', 'ilike', "%{$hold}%"),
                            );
                    }),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        function (Builder $query) {
                            return $query
                                ->whereRelation('careTeam', 'user_id', '=', auth()->id())
                                ->get();
                        }
                    ),
            ])
            ->actions([
                ViewAction::make(),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'students'),
                    DeleteBulkAction::make(),
                    ToggleCareTeamBulkAction::make(),
                ]),
            ]);
    }

    protected function caseloadFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $query->whereKey(
            app(TranslateCaseloadFilters::class)
                ->handle($data['value'])
                ->pluck($query->getModel()->getQualifiedKeyName()),
        );
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
