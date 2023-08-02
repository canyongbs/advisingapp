<?php

namespace Assist\AssistDataModel\Models;

use Eloquent;
use App\Models\BaseModel;
use Assist\Case\Models\CaseItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Assist\AssistDataModel\Models\Student
 *
 * @property-read Collection<int, CaseItem> $cases
 * @property-read int|null $cases_count
 *
 * @method static \Assist\AssistDataModel\Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 *
 * @mixin Eloquent
 */
class Student extends BaseModel
{
    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    public function cases(): MorphMany
    {
        return $this->morphMany(
            related: CaseItem::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'sisid'
        );
    }
}
