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

namespace Assist\CaseloadManagement\Models;

use App\Models\User;
use App\Models\BaseModel;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Assist\CaseloadManagement\Enums\CaseloadType;
use Assist\CaseloadManagement\Enums\CaseloadModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Assist\CaseloadManagement\Actions\TranslateCaseloadFilters;

/**
 * @mixin IdeHelperCaseload
 */
class Caseload extends BaseModel
{
    protected $fillable = [
        'query',
        'filters',
        'name',
        'description',
        'model',
        'type',
    ];

    protected $casts = [
        'filters' => 'array',
        'model' => CaseloadModel::class,
        'type' => CaseloadType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(CaseloadSubject::class);
    }

    public function retrieveRecords(): Collection
    {
        if (count($this->subjects) > 0) {
            return $this->subjects->map(function (CaseloadSubject $subject) {
                return $subject->subject;
            });
        }

        /** @var Builder $modelQueryBuilder */
        $modelQueryBuilder = $this->model->query();

        $class = $this->model->class();

        return $modelQueryBuilder
            ->whereKey(
                resolve(TranslateCaseloadFilters::class)
                    ->handle($this)
                    ->pluck(resolve($class)->getKeyName()),
            )
            ->get();
    }
}
