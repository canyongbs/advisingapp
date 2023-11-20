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

namespace App\Concerns;

use Exception;
use Filament\Tables\Columns\Column;
use Assist\Prospect\Models\Prospect;
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

        if ($filterWithOpenSearchQuery) {
            $query->whereIn(
                'id',
                Prospect::searchQuery($openSearchQuery)
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
