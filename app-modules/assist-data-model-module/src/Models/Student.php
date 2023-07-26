<?php

namespace Assist\AssistDataModel\Models;

use App\Models\BaseModel;
use App\Models\IdeHelperStudent;
use Assist\Case\Models\CaseItem;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * @mixin IdeHelperStudent
 */
class Student extends BaseModel
{
    protected $primaryKey = 'student_id';

    public $incrementing = false;

    public function cases(): MorphMany
    {
        return $this->morphMany(
            related: CaseItem::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'student_id'
        );
    }
}
