<?php

namespace Assist\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Tables\Table;
use App\Filament\Columns\IdColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\Column;
use Assist\Prospect\Models\Prospect;
use App\Filament\Actions\ImportAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use OpenSearch\Adapter\Documents\Document;
use Filament\Tables\Actions\BulkActionGroup;
use Assist\Prospect\Imports\ProspectImporter;
use Filament\Tables\Actions\DeleteBulkAction;
use OpenSearch\ScoutDriverPlus\Support\Query;
use OpenSearch\ScoutDriverPlus\Decorators\Hit;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Engagement\Filament\Actions\BulkEngagementAction;
use Assist\Notifications\Filament\Actions\SubscribeBulkAction;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;
use Assist\Notifications\Filament\Actions\SubscribeTableAction;

class ListProspects extends ListRecords
{
    protected static string $resource = ProspectResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make(Prospect::displayNameKey())
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->translateLabel()
                    ->searchable('email^4')
                    ->sortable(),
                TextColumn::make('mobile')
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
                        return $record->status->color;
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
                SelectFilter::make('caseload')
                    ->options(
                        auth()->user()->caseloads()
                            ->where('model', CaseloadModel::Prospect)
                            ->pluck('name', 'id'),
                    )
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'])) {
                            return;
                        }

                        $query->whereKey(
                            app(TranslateCaseloadFilters::class)
                                ->handle($data['value'])
                                ->pluck($query->getModel()->getQualifiedKeyName()),
                        );
                    }),
                SelectFilter::make('status')
                    ->relationship('status', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('source')
                    ->relationship('source', 'name')
                    ->multiple()
                    ->preload(),
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
                ]),
            ]);
    }

    //public function filterTableQuery(Builder $query): Builder {}

    //public function getTableRecords(): Collection | Paginator {}

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        //$this->applyColumnSearchesToTableQuery($query);

        $fields = collect($this->getTable()->getColumns())->map(function (Column $column) {
            return ! $column->isHidden() && $column->isGloballySearchable() ? $column->getSearchColumns() : null;
        })
            ->whereNotNull()
            ->flatten()
            ->toArray();

        if (filled($search = $this->getTableSearch())) {
            //ray(Prospect::searchQuery(
            //    Query::bool()
            //        ->must(
            //            Query::multiMatch()
            //                ->fields($fields)
            //                ->type('bool_prefix')
            //                ->query($search)
            //                ->fuzziness('AUTO')
            //        )
            //        ->filter(
            //            Query::term()
            //                ->field('status_id')
            //                ->value('9a5a0b30-b5f0-4e17-9f07-7704684d6341')
            //        )
            //)
            //    ->execute()
            //    ->hits());

            // TODO: Look into getting the table to respect the order of the results from the search
            $query->whereIn(
                'id',
                Prospect::searchQuery(
                    Query::bool()
                        ->must(
                            Query::multiMatch()
                                ->fields($fields)
                                ->type('bool_prefix')
                                ->query($search)
                                ->fuzziness('AUTO')
                        )
                    //->filter(
                    //    Query::term()
                    //        ->field('status_id')
                    //        ->value('9a5a0b30-b5f0-4e17-9f07-7704684d6341')
                    //)
                )
                    ->execute()
                    ->documents()
                    ->map(fn (Document $document) => $document->id())
            );
        }

        return $query;
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
