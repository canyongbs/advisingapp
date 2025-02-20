<?php

namespace AdvisingApp\CaseManagement\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read Educatable $assignee
 *
 * @mixin IdeHelperCaseFeedback
 */
class CaseFeedback extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'csat_answer',
        'nps_answer',
        'case_id',
        'assignee_id',
        'assignee_type',
    ];

    public function assignee(): MorphTo
    {
        return $this->morphTo(
            name: 'assignee',
            type: 'assignee_type',
            id: 'assignee_id',
        );
    }

    public function case(): BelongsTo
    {
        return $this->belongsTo(CaseModel::class, 'case_id', 'id');
    }
}
