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

namespace Assist\Alert\Filament\Resources\AlertResource\Pages;

use Filament\Tables\Table;
use Assist\Alert\Models\Alert;
use Filament\Infolists\Infolist;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Assist\Alert\Enums\AlertStatus;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Group;
use Assist\Alert\Enums\AlertSeverity;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Filament\Forms\Components\MorphToSelect;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\CaseloadManagement\Models\Caseload;
use Filament\Forms\Components\MorphToSelect\Type;
use Assist\Alert\Filament\Resources\AlertResource;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use Assist\AssistDataModel\Filament\Resources\StudentResource\Pages\ManageStudentAlerts;

class ListAlerts extends ListRecords
{
    protected static string $resource = AlertResource::class;

    // TODO: Change this to a link to the students page when tableAction link triggering becomes available in Filament 3.1
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => ManageStudentAlerts::getUrl(['record' => $record->concern]),
                        Prospect::class => ManageProspectAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    }),
                TextEntry::make('description'),
                TextEntry::make('severity'),
                TextEntry::make('suggested_intervention'),
                TextEntry::make('status'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('concern.display_name')
                    ->label('Related To')
                    ->getStateUsing(fn (Alert $record): ?string => $record->concern?->{$record->concern::displayNameKey()})
                    ->url(fn (Alert $record) => match ($record->concern ? $record->concern::class : null) {
                        Student::class => ManageStudentAlerts::getUrl(['record' => $record->concern]),
                        Prospect::class => ManageProspectAlerts::getUrl(['record' => $record->concern]),
                        default => null,
                    })
                    ->searchable(query: fn (Builder $query, $search) => $query->educatableSearch(relationship: 'concern', search: $search))
                    ->forceSearchCaseInsensitive()
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->limit(),
                TextColumn::make('severity')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('subscribed')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            relation: 'concern',
                            callback: fn (Builder $query) => $query->whereRelation('subscriptions', 'user_id', auth()->id())
                        )
                    ),
                Filter::make('care_team')
                    ->label('Care Team')
                    ->query(
                        fn (Builder $query): Builder => $query->whereHas(
                            relation: 'concern',
                            callback: fn (Builder $query) => $query->whereRelation('careTeam', 'user_id', auth()->id())
                        )
                    ),
                SelectFilter::make('my_caseloads')
                    ->label('My Caseloads')
                    ->options(
                        auth()->user()->caseloads()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                SelectFilter::make('all_caseloads')
                    ->label('All Caseloads')
                    ->options(
                        Caseload::all()
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                SelectFilter::make('severity')
                    ->options(AlertSeverity::class),
                SelectFilter::make('status')
                    ->options(AlertStatus::class)
                    ->multiple()
                    ->default([AlertStatus::Active->value]),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->form([
                    MorphToSelect::make('concern')
                        ->label('Related To')
                        ->types([
                            Type::make(Student::class)
                                ->titleAttribute(Student::displayNameKey()),
                            Type::make(Prospect::class)
                                ->titleAttribute(Prospect::displayNameKey()),
                        ])
                        ->searchable()
                        ->required(),
                    Group::make()
                        ->schema([
                            Textarea::make('description')
                                ->required()
                                ->string(),
                            Select::make('severity')
                                ->options(AlertSeverity::class)
                                ->selectablePlaceholder(false)
                                ->default(AlertSeverity::default())
                                ->required()
                                ->enum(AlertSeverity::class),
                            Textarea::make('suggested_intervention')
                                ->required()
                                ->string(),
                            Select::make('status')
                                ->options(AlertStatus::class)
                                ->selectablePlaceholder(false)
                                ->default(AlertStatus::default())
                                ->required()
                                ->enum(AlertStatus::class),
                        ])
                        ->columns(),
                ]),
        ];
    }

    protected function caseloadFilter(Builder $query, array $data): void
    {
        if (blank($data['value'])) {
            return;
        }

        $caseload = Caseload::find($data['value']);

        /** @var Model $model */
        $model = resolve($caseload->model->class());

        $query->whereIn(
            'concern_id',
            app(TranslateCaseloadFilters::class)
                ->handle($data['value'])
                ->pluck($model->getQualifiedKeyName()),
        );
    }
}
