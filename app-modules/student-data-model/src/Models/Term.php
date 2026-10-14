<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\StudentDataModel\Models;

use AdvisingApp\StudentDataModel\Database\Factories\TermFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperTerm
 */
class Term extends Model
{
    /** @use HasFactory<TermFactory> */
    use HasFactory;

    use UsesTenantConnection;

    protected $table = 'terms';

    /**
     * This Model has a primary key that is auto generated as a v4 UUID by Postgres.
     * We do so so that we can do things like view, edit, and delete a specific record in the UI / API.
     * This ID should NEVER be used for relationships as these records do not belong to our system. Use `sis_term_id` instead.
     */
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'sis_term_id',
        'name',
        'code',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Term names are not unique in the SIS, so the start month is included to tell them apart (e.g. `FA-20 (Sep 2020)`).
     */
    public function getDisplayName(): string
    {
        return $this->start_date
            ? "{$this->name} ({$this->start_date->format('M Y')})"
            : $this->name;
    }

    /**
     * @return HasMany<StudentTermAttribute, $this>
     */
    public function studentTermAttributes(): HasMany
    {
        return $this->hasMany(StudentTermAttribute::class, 'sis_term_id', 'sis_term_id');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'sis_term_id', 'sis_term_id');
    }
}
