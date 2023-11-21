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

use Illuminate\Validation\ValidationException;

trait HasAdvancedFilter
{
    public function scopeAdvancedFilter($query, $data)
    {
        return $this->processQuery($query, $data);
    }

    public function processQuery($query, $data)
    {
        $data = $this->processGlobalSearch($data);

        $v = validator()->make($data, [
            's' => 'sometimes|nullable|string',
            'order_column' => 'sometimes|required|in:' . $this->orderableColumns(),
            'order_direction' => 'sometimes|required|in:asc,desc',
            // 'limit'           => 'sometimes|required|integer|min:1',

            // advanced filter
            'filter_match' => 'sometimes|required|in:and,or',
            'f' => 'sometimes|required|array',
            'f.*.column' => 'required|in:' . $this->whiteListColumns(),
            'f.*.operator' => 'required_with:f.*.column|in:' . $this->allowedOperators(),
            'f.*.query_1' => 'required',
            'f.*.query_2' => 'required_if:f.*.operator,between,not_between',
        ]);

        if ($v->fails()) {
            throw new ValidationException($v);
        }

        $data = $v->validated();

        return (new FilterQueryBuilder())->apply($query, $data);
    }

    protected function orderableColumns()
    {
        return implode(',', $this->orderable);
    }

    protected function whiteListColumns()
    {
        return implode(',', $this->filterable);
    }

    protected function allowedOperators()
    {
        return implode(',', [
            'contains',
        ]);
    }

    protected function processGlobalSearch($data)
    {
        if (isset($data['f']) || ! isset($data['s'])) {
            return $data;
        }

        $data['filter_match'] = 'or';

        $data['f'] = array_map(function ($column) use ($data) {
            return [
                'column' => $column,
                'operator' => 'contains',
                'query_1' => $data['s'],
            ];
        }, $this->filterable);

        return $data;
    }
}
