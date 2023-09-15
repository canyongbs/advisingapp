<?php

namespace App\Models;

use Eloquent;
use Carbon\Carbon;
use DateTimeInterface;
use Spatie\MediaLibrary\HasMedia;
use App\Support\HasAdvancedFilter;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

/**
 * App\Models\EngagementStudentFile
 *
 * @property int $id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $student_id
 * @property-read mixed $file
 * @property-read MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 *
 * @method static Builder|EngagementStudentFile advancedFilter($data)
 * @method static Builder|EngagementStudentFile newModelQuery()
 * @method static Builder|EngagementStudentFile newQuery()
 * @method static Builder|EngagementStudentFile onlyTrashed()
 * @method static Builder|EngagementStudentFile query()
 * @method static Builder|EngagementStudentFile whereCreatedAt($value)
 * @method static Builder|EngagementStudentFile whereDeletedAt($value)
 * @method static Builder|EngagementStudentFile whereDescription($value)
 * @method static Builder|EngagementStudentFile whereId($value)
 * @method static Builder|EngagementStudentFile whereStudentId($value)
 * @method static Builder|EngagementStudentFile whereUpdatedAt($value)
 * @method static Builder|EngagementStudentFile withTrashed()
 * @method static Builder|EngagementStudentFile withoutTrashed()
 *
 * @mixin Eloquent
 */
class EngagementStudentFile extends BaseModel implements HasMedia
{
    use HasAdvancedFilter;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $appends = [
        'file',
    ];

    protected $fillable = [
        'description',
        'student_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public $orderable = [
        'id',
        'description',
        'student.full',
        'student.sisid',
        'student.otherid',
    ];

    public $filterable = [
        'id',
        'description',
        'student.full',
        'student.sisid',
        'student.otherid',
    ];

    public function getFileAttribute()
    {
        return $this->getMedia('engagement_student_file_file')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(RecordStudentItem::class);
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getUpdatedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    public function getDeletedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('project.datetime_format')) : null;
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}
