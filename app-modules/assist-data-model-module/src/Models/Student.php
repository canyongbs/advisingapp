<?php

namespace Assist\AssistDataModelModule\Models;

use App\Models\IdeHelperStudent;
use Assist\CaseModule\Models\CaseItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperStudent
 */
class Student extends Model
{
    use HasFactory;

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
