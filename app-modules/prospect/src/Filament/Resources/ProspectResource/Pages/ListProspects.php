<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Assist\Prospect\Models\Prospect;
use App\Filament\Actions\ImportAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Concerns\FilterTableWithOpenSearch;
use Filament\Tables\Actions\BulkActionGroup;
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
        return [
            ImportAction::make()
                ->importer(ProspectImporter::class)
                ->authorize('import', Prospect::class),
            CreateAction::make(),
        ];
    }
}
