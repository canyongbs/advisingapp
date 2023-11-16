<?php

namespace Assist\KnowledgeBase\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Assist\Division\Models\Division;
use App\Models\Contracts\IsSearchable;
use OwenIt\Auditing\Contracts\Auditable;
use OpenSearch\ScoutDriverPlus\Searchable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperKnowledgeBaseItem
 */
class KnowledgeBaseItem extends BaseModel implements Auditable, HasMedia, IsSearchable
{
    use AuditableTrait;
    use HasUuids;
    use InteractsWithMedia;
    use Searchable;

    protected $casts = [
        'public' => 'boolean',
    ];

    protected $fillable = [
        'question',
        'quality_id',
        'status_id',
        'public',
        'category_id',
        'solution',
        'notes',
    ];

    public function searchableAs(): string
    {
        return config('scout.prefix') . 'knowledge_base_items';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->getScoutKey(),
            'question' => $this->question,
            'public' => $this->public,
            'solution' => $this->solution,
            'notes' => $this->notes,
            'quality_id' => $this->quality_id,
            'status_id' => $this->status_id,
            'category_id' => $this->category_id,
        ];
    }

    public function quality(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseQuality::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseStatus::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseCategory::class);
    }

    public function division(): BelongsToMany
    {
        return $this->belongsToMany(Division::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('solution');
        $this->addMediaCollection('notes');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
