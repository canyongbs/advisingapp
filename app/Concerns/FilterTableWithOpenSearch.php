<?php

namespace App\Concerns;

use Exception;
use Filament\Tables\Columns\Column;
use Assist\Prospect\Models\Prospect;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use OpenSearch\Adapter\Documents\Document;
use OpenSearch\ScoutDriverPlus\Support\Query;
use App\Filament\Columns\OpenSearch\OpenSearchColumn;
use App\Filament\Filters\OpenSearch\OpenSearchFilter;
use OpenSearch\ScoutDriverPlus\Builders\QueryBuilderInterface;

trait FilterTableWithOpenSearch
{
    public function filterTableQuery(Builder $query): Builder
    {
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

        collect($this->getTable()->getFilters())
            ->each(function (BaseFilter $filter) use (&$openSearchQuery, &$filterWithOpenSearchQuery) {
                if (! $filter instanceof OpenSearchFilter) {
                    throw new Exception('Unsupported filter type used on table');
                }

                /** @var OpenSearchFilter $filter */
                $filterQuery = $filter->openSearchQuery($this->getTableFiltersForm()->getRawState()[$filter->getName()]);

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
            default => throw new Exception('Unsupported filter type used on table')
        };
    }

    protected function selectFilterOpenSearchQuery(BaseFilter $filter, $state): ?QueryBuilderInterface
    {
        return ! empty($state['values']) ? Query::terms()
            ->field($filter->getName())
            ->values($state['values']) : null;
    }
}
