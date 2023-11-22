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

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FilterQueryBuilder
{
    protected $model;

    protected $table;

    public function apply($query, $data)
    {
        $this->model = $query->getModel();
        $this->table = $this->model->getTable();

        if (isset($data['f'])) {
            foreach ($data['f'] as $filter) {
                $filter['match'] = $data['filter_match'] ?? 'and';
                $this->makeFilter($query, $filter);
            }
        }

        $this->makeOrder($query, $data);

        return $query;
    }

    public function contains($filter, $query)
    {
        $filter['query_1'] = addslashes($filter['query_1']);

        return $query->where($filter['column'], 'like', '%' . $filter['query_1'] . '%', $filter['match']);
    }

    protected function makeOrder($query, $data)
    {
        if ($this->isNestedColumn($data['order_column'])) {
            [$relationship, $column] = explode('.', $data['order_column']);
            $callable = Str::camel($relationship);
            $belongs = $this->model->{$callable}(
            );
            $relatedModel = $belongs->getModel();
            $relatedTable = $relatedModel->getTable();
            $as = "prefix_{$relatedTable}";

            if (! $belongs instanceof BelongsTo) {
                return;
            }

            $query->leftJoin(
                "{$relatedTable} as {$as}",
                "{$as}.id",
                '=',
                "{$this->table}.{$relationship}_id"
            );

            $data['order_column'] = "{$as}.{$column}";
        }

        $query
            ->orderBy($data['order_column'], $data['order_direction'])
            ->select("{$this->table}.*");
    }

    protected function makeFilter($query, $filter)
    {
        if ($this->isNestedColumn($filter['column'])) {
            [$relation, $filter['column']] = explode('.', $filter['column']);
            $callable = Str::camel($relation);
            $filter['match'] = 'and';

            $query->orWhereHas(Str::camel($callable), function ($q) use ($filter) {
                $this->{Str::camel($filter['operator'])}(
                    $filter,
                    $q
                );
            });
        } else {
            $filter['column'] = "{$this->table}.{$filter['column']}";
            $this->{Str::camel($filter['operator'])}(
                $filter,
                $query
            );
        }
    }

    protected function isNestedColumn($column)
    {
        return strpos($column, '.') !== false;
    }
}
