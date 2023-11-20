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

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Tables\Table;
use Filament\Actions\Action;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\StaticAction;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use App\Filament\Actions\ImportAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use App\Concerns\FilterTableWithOpenSearch;
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Assist\Prospect\Imports\ProspectImporter;
use Filament\Tables\Actions\DeleteBulkAction;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\CareTeam\Filament\Actions\ToggleCareTeamBulkAction;
use Assist\Notifications\Filament\Actions\SubscribeBulkAction;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;
use Assist\Notifications\Filament\Actions\SubscribeTableAction;
use App\Filament\Columns\OpenSearch\TextColumn as OpenSearchTextColumn;
use App\Filament\Filters\OpenSearch\SelectFilter as OpenSearchSelectFilter;

class ListProspects extends ListRecords
{
    use FilterTableWithOpenSearch;

    protected static string $resource = ProspectResource::class;

    public array $engageActionData = [];

    public array $engageActionRecords = [];

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                OpenSearchTextColumn::make(Prospect::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                OpenSearchTextColumn::make('email')
                    ->label('Email')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                OpenSearchTextColumn::make('mobile')
                    ->label('Mobile')
                    ->translateLabel()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->translateLabel()
                    ->state(function (Prospect $record) {
                        return $record->status->name;
                    })
                    ->color(function (Prospect $record) {
                        return $record->status->color->value;
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query
                            ->join('prospect_statuses', 'prospects.status_id', '=', 'prospect_statuses.id')
                            ->orderBy('prospect_statuses.name', $direction);
                    }),
                TextColumn::make('source.name')
                    ->label('Source')
                    ->translateLabel()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->translateLabel()
                    ->dateTime('g:ia - M j, Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('my_caseloads')
                    ->label('My Caseloads')
                    ->options(
                        auth()->user()->caseloads()
                            ->where('model', CaseloadModel::Prospect)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                SelectFilter::make('all_caseloads')
                    ->label('All Caseloads')
                    ->options(
                        Caseload::all()
                            ->where('model', CaseloadModel::Prospect)
                            ->pluck('name', 'id'),
                    )
                    ->searchable()
                    ->optionsLimit(20)
                    ->query(fn (Builder $query, array $data) => $this->caseloadFilter($query, $data)),
                OpenSearchSelectFilter::make('status_id')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                OpenSearchSelectFilter::make('source_id')
                    ->relationship('source', 'name')
                    ->multiple()
                    ->preload(),
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
                EditAction::make(),
                SubscribeTableAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    SubscribeBulkAction::make(),
                    BulkEngagementAction::make(context: 'prospects'),
                    DeleteBulkAction::make(),
                    ToggleCareTeamBulkAction::make(),
                    BulkAction::make('bulk_update')
                        ->icon('heroicon-o-pencil-square')
                        ->form([
                            Select::make('field')
                                ->options([
                                    'assigned_to_id' => 'Assigned To',
                                    'description' => 'Description',
                                    'email_bounce' => 'Email Bounce',
                                    'hsgrad' => 'High School Graduation Date',
                                    'sms_opt_out' => 'SMS Opt Out',
                                    'source_id' => 'Source',
                                    'status_id' => 'Status',
                                ])
                                ->required()
                                ->live(),
                            Select::make('assigned_to_id')
                                ->label('Assigned To')
                                ->relationship('assignedTo', 'name')
                                ->searchable()
                                ->exists(
                                    table: (new User())->getTable(),
                                    column: (new User())->getKeyName()
                                )
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'assigned_to_id'),
                            Textarea::make('description')
                                ->string()
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'description'),
                            Radio::make('email_bounce')
                                ->label('Email Bounce')
                                ->boolean()
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'email_bounce'),
                            TextInput::make('hsgrad')
                                ->label('High School Graduation Date')
                                ->numeric()
                                ->minValue(1920)
                                ->maxValue(now()->addYears(25)->year)
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'hsgrad'),
                            Radio::make('sms_opt_out')
                                ->label('SMS Opt Out')
                                ->boolean()
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'sms_opt_out'),
                            Select::make('source_id')
                                ->label('Source')
                                ->relationship('source', 'name')
                                ->exists(
                                    table: (new ProspectSource())->getTable(),
                                    column: (new ProspectSource())->getKeyName()
                                )
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'source_id'),
                            Select::make('status_id')
                                ->label('Status')
                                ->relationship('status', 'name')
                                ->exists(
                                    table: (new ProspectStatus())->getTable(),
                                    column: (new ProspectStatus())->getKeyName()
                                )
                                ->required()
                                ->visible(fn (Get $get) => $get('field') === 'status_id'),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(
                                fn (Prospect $prospect) => $prospect
                                    ->forceFill([$data['field'] => $data[$data['field']]])
                                    ->save()
                            );

                            Notification::make()
                                ->title($records->count() . ' ' . str('Prospect')->plural($records->count()) . ' Updated')
                                ->success()
                                ->send();
                        }),
                ]),
            ]);
    }

    public function cancelEngageAction(): Action
    {
        return Action::make('cancelEngage')
            ->label('Cancel')
            ->mountUsing(function () {
                $this->engageActionData = $this->mountedTableBulkActionData;
                $this->engageActionRecords = $this->selectedTableRecords;

                $this->unmountTableBulkAction();
            })
            ->requiresConfirmation()
            ->modalSubmitAction(fn (StaticAction $action) => $action->color('danger'))
            ->action(function () {
                $this->engageActionData = [];
                $this->engageActionRecords = [];
            })
            ->modalDescription(fn () => 'The message has not been sent, are you sure you wish to return to the list view?')
            ->closeModalByClickingAway(false)
            ->modalCloseButton(false)
            ->modalCancelAction(false)
            ->extraModalFooterActions([
                Action::make('restoreEngageBulkAction')
                    ->label('Cancel')
                    ->action(function () {
                        $this->mountTableBulkAction('engage');

                        $this->mountedTableBulkActionData = $this->engageActionData;
                        $this->selectedTableRecords = $this->engageActionRecords;
                    })
                    ->cancelParentActions(),
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
        return [
            ImportAction::make()
                ->importer(ProspectImporter::class)
                ->authorize('import', Prospect::class),
            CreateAction::make(),
        ];
    }
}
