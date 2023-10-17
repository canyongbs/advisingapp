<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Prospect\Models\Prospect;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Prunable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperEngagementFile
 */
class EngagementFile extends BaseModel implements HasMedia, Auditable
{
    use InteractsWithMedia;
    use AuditableTrait;
    use Prunable;

    protected $fillable = [
        'description',
        'retention_date',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('file')
            ->useDisk('s3')
            ->singleFile();
    }

    public function students(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'entity',
            table: 'engagement_file_entities',
            foreignPivotKey: 'engagement_file_id',
            relatedPivotKey: 'entity_id',
            relation: 'engagementFiles',
        )
            ->using(EngagementFileEntities::class)
            ->withTimestamps();
    }

    public function prospects(): MorphToMany
    {
        return $this->morphedByMany(
            related: Prospect::class,
            name: 'entity',
            table: 'engagement_file_entities',
            foreignPivotKey: 'engagement_file_id',
            relatedPivotKey: 'entity_id',
            relation: 'prospects',
        )
            ->using(EngagementFileEntities::class)
            ->withTimestamps();
    }

    public function prunable(): Builder
    {
        return static::where(
            'retention_date',
            '<',
            now()->startOfDay(),
        );
    }
}
