<?php

namespace AdvisingApp\Prospect\Models;

use App\Models\User;
use AdvisingApp\Segment\Models\Segment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class Pipeline extends Model implements Auditable
{
    use HasFactory;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'description',
        'segment_id',
        'user_id',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class, 'pipeline_id');
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(Segment::class, 'segment_id');
    }

    /**
     * @return MorphToMany<Prospect>
     */
    public function educatables(): MorphToMany
    {
        return $this->morphedByMany(
            related: Prospect::class,
            name: 'educatable',
            table: 'pipeline_educatable',
            foreignPivotKey: 'pipeline_id',
            relatedPivotKey: 'educatable_id',
        )
            ->using(PipelineEductable::class)
            ->withPivot(['pipeline_stage_id'])
            ->withTimestamps();
    }
}
