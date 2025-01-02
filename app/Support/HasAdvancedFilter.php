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
