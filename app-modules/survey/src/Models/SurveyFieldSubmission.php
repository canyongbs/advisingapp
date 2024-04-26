<?php

namespace AdvisingApp\Survey\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyFieldSubmission extends Pivot
{
    use HasUuids;

    protected $fillable = [
        'response',
        'id',
    ];

    protected $casts = [
        'response' => 'array',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(SurveyField::class, 'field_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(SurveySubmission::class, 'submission_id');
    }
}
