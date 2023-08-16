<?php

namespace Assist\Engagement\Models;

use Eloquent;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use OwenIt\Auditing\Models\Audit;
use Assist\Prospect\Models\Prospect;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\AssistDataModel\Models\Student;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Engagement\Database\Factories\EngagementFileFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

/**
 * Assist\Engagement\Models\EngagementFile
 *
 * @property string $id
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read Collection<int, Prospect> $prospects
 * @property-read int|null $prospects_count
 * @property-read Collection<int, Student> $students
 * @property-read int|null $students_count
 *
 * @method static EngagementFileFactory factory($count = null, $state = [])
 * @method static Builder|EngagementFile newModelQuery()
 * @method static Builder|EngagementFile newQuery()
 * @method static Builder|EngagementFile query()
 * @method static Builder|EngagementFile whereCreatedAt($value)
 * @method static Builder|EngagementFile whereDescription($value)
 * @method static Builder|EngagementFile whereId($value)
 * @method static Builder|EngagementFile whereUpdatedAt($value)
 *
 * @mixin Eloquent
 */
class EngagementFile extends BaseModel implements HasMedia, Auditable
{
    use HasUuids;
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
