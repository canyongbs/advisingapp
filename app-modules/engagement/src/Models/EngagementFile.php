<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class EngagementFile extends BaseModel implements HasMedia
{
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

    public function student(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'engagement_file_entity',
        );
    }

    public function prospect(): MorphToMany
    {
        return $this->morphedByMany(Prospect::class, 'engagement_file_entity');
    }
}
