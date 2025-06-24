<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\Concerns\CanAddAssistantLicenseGlobalScope;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\File;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @mixin IdeHelperQnaAdvisor
 */
class QnaAdvisor extends BaseModel implements HasMedia
{
    use CanAddAssistantLicenseGlobalScope;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'archived_at',
        'name',
        'model',
        'description',
        'instructions',
        'knowledge',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
        'model' => AiModel::class,
    ];

    /**
     * @return HasMany<QnaAdvisorCategory, $this>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(QnaAdvisorCategory::class, 'qna_advisor_id');
    }

    /**
     * @return HasManyThrough<QnaAdvisorQuestion, QnaAdvisorCategory, $this>
     */
    public function questions(): HasManyThrough
    {
        return $this->hasManyThrough(
            QnaAdvisorQuestion::class,
            QnaAdvisorCategory::class,
            'qna_advisor_id',
            'category_id',
            'id',
            'id'
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->acceptsFile(function (File $file) {
                return in_array($file->mimeType, [
                    'image/png',
                    'image/jpeg',
                    'image/gif',
                ]);
            });
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);

        $this->addMediaConversion('thumbnail')
            ->performOnCollections('avatar')
            ->width(32)
            ->height(32);
    }
}
