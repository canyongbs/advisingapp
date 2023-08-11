<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class EngagementFile extends BaseModel implements HasMedia
{
    use HasUuids;
    use InteractsWithMedia;

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
            name: 'engagement_file',
            table: 'engagement_file_entities',
            foreignPivotKey: 'engagement_file_id',
            relatedPivotKey: 'entity_id',
            relation: 'student',
        );
    }

    public function prospects(): MorphToMany
    {
        return $this->morphedByMany(Prospect::class, 'engagement_file');
    }
}
