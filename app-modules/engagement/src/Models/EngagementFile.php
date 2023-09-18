<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Prospect\Models\Prospect;
use OwenIt\Auditing\Contracts\Auditable;
use Assist\AssistDataModel\Models\Student;
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

    protected $fillable = [
        'description',
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
}
