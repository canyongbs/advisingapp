<?php

namespace App\Concerns;

use Filament\Tables\Columns\Column;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use OpenSearch\Adapter\Documents\Document;
use OpenSearch\ScoutDriverPlus\Support\Query;
use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

trait FilterTableWithOpenSearch
{
    public function filterTableQuery(Builder $query): Builder
    {
        // Search

        $fields = collect($this->getTable()->getColumns())->map(function (Column $column) {
            return ! $column->isHidden() && $column->isGloballySearchable() ? $column->getSearchColumns() : null;
        })
            ->merge(
                collect($this->getTableColumnSearches())->map(function ($search, $column) {
                    $column = $this->getTable()->getColumn($column);

                    if (blank($search) || ! $column || $column->isHidden() || ! $column->isIndividuallySearchable()) {
                        return null;
                    }

                    return $column->getSearchColumns();
                })
            )
            ->whereNotNull()
            ->flatten()
            ->toArray();

        $openSearchQuery = Query::bool();
        $filterWithOpenSearchQuery = false;

        if (filled($search = $this->getTableSearch())) {
            $filterWithOpenSearchQuery = true;

            $openSearchQuery->must(
                Query::multiMatch()
                    ->fields($fields)
                    ->type('bool_prefix')
                    ->query($search)
                    ->fuzziness('AUTO')
            );
        }

        // Filtering

        $filters = collect($this->getTableFiltersForm()->getRawState())
            ->keys()
            ->mapWithKeys(function ($filterKey) {
                return [$filterKey => [
                    'filter' => $this->getTable()->getFilter($filterKey),
                    'state' => $this->getTableFiltersForm()->getRawState()[$filterKey],
                ]];
            });

        $filters->each(function ($filter) use (&$openSearchQuery, &$filterWithOpenSearchQuery) {
            $filterQuery = $this->generateFilterOpenSearchQuery($filter['filter'], $filter['state']);

            if ($filterQuery) {
                $filterWithOpenSearchQuery = true;

                $openSearchQuery->filter($filterQuery);
            }
        });

        if ($filterWithOpenSearchQuery) {
            $query->whereIn(
                'id',
                Prospect::searchQuery($openSearchQuery)
                    ->execute()
                    ->documents()
                    ->map(fn (Document $document) => $document->id())
            );
        }

        //$this->applyFiltersToTableQuery($query);

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

    public function generateFilterOpenSearchQuery(mixed $filter, mixed $state): ?QueryBuilderInterface
    {
        return match (true) {
            $filter instanceof SelectFilter => $this->selectFilterOpenSearchQuery($filter, $state),
            default => null,
        };
    }

    protected function selectFilterOpenSearchQuery(BaseFilter $filter, $state): ?QueryBuilderInterface
    {
        return ! empty($state['values']) ? Query::terms()
            ->field($filter->getName())
            ->values($state['values']) : null;
    }
}
