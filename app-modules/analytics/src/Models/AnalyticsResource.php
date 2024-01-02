<?php

namespace AdvisingApp\Analytics\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AdvisingApp\Analytics\Enums\AnalyticsResourceCategory;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

class AnalyticsResource extends BaseModel implements Auditable, HasMedia
{
    use AuditableTrait;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'description',
        'url',
        'category',
        'is_active',
    ];

    protected $casts = [
        'category' => AnalyticsResourceCategory::class,
        'is_active' => 'boolean',
        'is_included_in_data_portal' => 'boolean',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')
            ->singleFile();
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(AnalyticsResourceSource::class);
    }
}
