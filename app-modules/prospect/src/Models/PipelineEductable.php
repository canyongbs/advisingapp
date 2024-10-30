<?php

namespace AdvisingApp\Prospect\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class PipelineEductable extends MorphPivot
{
    use HasFactory;

    protected $table = 'pipeline_educatable';

    public function educatable()
    {
        return $this->morphTo();
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
}
