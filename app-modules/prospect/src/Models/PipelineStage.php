<?php

namespace AdvisingApp\Prospect\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PipelineStage extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'name',
        'pipeline_id',
        'is_default',
        'order',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
}
