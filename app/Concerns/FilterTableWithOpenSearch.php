<?php

namespace App\Concerns;

use Exception;
use Filament\Tables\Columns\Column;
use App\Models\Contracts\IsSearchable;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use OpenSearch\Adapter\Documents\Document;
use OpenSearch\ScoutDriverPlus\Support\Query;
use App\Filament\Columns\OpenSearch\OpenSearchColumn;
use App\Filament\Filters\OpenSearch\OpenSearchFilter;

trait FilterTableWithOpenSearch
{
    public function filterTableQuery(Builder $query): Builder
    {
        if (config('scout.driver') !== 'opensearch') {
            return parent::filterTableQuery($query);
        }

        $openSearchQuery = Query::bool();
        $filterWithOpenSearchQuery = false;

        // Search

        if (filled($search = $this->getTableSearch())) {
            collect($this->getTable()->getColumns())->mapWithKeys(function (Column $column) {
                return ! $column->isHidden() && $column->isGloballySearchable() ? [$column->getName() => $column] : [];
            })
                ->merge(
                    collect($this->getTableColumnSearches())->mapWithKeys(function ($search, $column) {
                        $column = $this->getTable()->getColumn($column);

                        if (blank($search) || ! $column || $column->isHidden() || ! $column->isIndividuallySearchable()) {
                            return [];
                        }

                        return [$column->getName() => $column];
                    })
                )
                ->each(function (Column $column, string $columnName) use (&$openSearchQuery, &$filterWithOpenSearchQuery, $search) {
                    if (! $column instanceof OpenSearchColumn) {
                        throw new Exception('Unsupported column type used on table');
                    }

                    /** @var OpenSearchColumn $column */
                    $query = $column->openSearchQuery($search);

                    $openSearchQuery->should($query);

                    $filterWithOpenSearchQuery = true;

                    $openSearchQuery->minimumShouldMatch(1);
                });
        }

        // Filtering

        [$openSearchFilters, $regularFilters] = collect($this->getTable()->getFilters())->partition(fn (BaseFilter $filter) => $filter instanceof OpenSearchFilter);

        $openSearchFilters
            ->each(function (BaseFilter $filter) use (&$openSearchQuery, &$filterWithOpenSearchQuery) {
                if (! $filter instanceof OpenSearchFilter) {
                    // Skip filters that don't support OpenSearch
                    return;
                }

                /** @var OpenSearchFilter $filter */
                $filterQuery = $filter->openSearchQuery($this->getTableFiltersForm()->getRawState()[$filter->getName()]);

                if ($filterQuery) {
                    $filterWithOpenSearchQuery = true;

                    $openSearchQuery->filter($filterQuery);
                }
            });

        /** @var IsSearchable $model */
        $model = app($this->getModel());

        if ($filterWithOpenSearchQuery) {
            $query->whereIn(
                $model->getKeyName(),
                $model::searchQuery($openSearchQuery)
                    ->execute()
                    ->documents()
                    ->map(fn (Document $document) => $document->id())
            );
        }

        $data = $this->getTableFiltersForm()->getRawState();

        $regularFilters->each(function (BaseFilter $filter) use ($query, $data) {
            $filter->applyToBaseQuery(
                $query,
                $data[$filter->getName()] ?? [],
            );
        });

        $query->where(function (Builder $query) use ($regularFilters, $data) {
            $regularFilters->each(function (BaseFilter $filter) use ($query, $data) {
                $filter->apply(
                    $query,
                    $data[$filter->getName()] ?? [],
                );
            });
        });

        foreach ($this->getTable()->getColumns() as $column) {
            if ($column->isHidden()) {
                continue;
            }

            $column->applyRelationshipAggregates($query);

            if ($this->getTable()->isGroupsOnly()) {
                continue;
            }

            $column->applyEagerLoading($query);
        }

        return $query;
    }
}
