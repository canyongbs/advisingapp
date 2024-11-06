<?php

namespace AdvisingApp\Prospect\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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


    public function educatables(): HasMany
    {
        return $this->hasMany(PipelineEductable::class,'pipeline_stage_id');
    }
}
