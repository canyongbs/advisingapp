<?php

namespace Assist\Engagement\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Assist\Engagement\Models\EngagementFile
 *
 * @property string $id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 * @property-read int|null $students_count
 *
 * @method static \Assist\Engagement\Database\Factories\EngagementFileFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile query()
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EngagementFile whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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
            name: 'entity',
            table: 'engagement_file_entities',
            foreignPivotKey: 'engagement_file_id',
            relatedPivotKey: 'entity_id',
            relation: 'student',
        );
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
        );
    }
}
