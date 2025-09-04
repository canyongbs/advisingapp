<?php

namespace AdvisingApp\Application\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ApplicationFieldSubmission extends Pivot
{
    protected $table = 'application_field_submission';

    protected $fillable = [
        'id',
        'response',
        'field_id',
        'submission_id',
    ];

    protected $casts = [
        'response' => 'array',
    ];
}
