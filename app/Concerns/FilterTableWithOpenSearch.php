<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace App\Concerns;

use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Tables\Columns\OpenSearch\OpenSearchColumn;
use App\Filament\Tables\Filters\OpenSearch\OpenSearchFilter;
use Exception;
use Filament\Tables\Columns\Column;
use Filament\Tables\Filters\BaseFilter;
use Illuminate\Database\Eloquent\Builder;
use OpenSearch\Adapter\Documents\Document;
use OpenSearch\ScoutDriverPlus\Support\Query;

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
