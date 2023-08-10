<?php

namespace Assist\KnowledgeBase\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Institution;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KnowledgeBaseItem extends BaseModel
{
    use SoftDeletes;

    protected $casts = [
        'public' => 'boolean',
    ];

    public static $search = [
        'question',
    ];

    public $orderable = [
        'id',
        'question',
        'quality.rating',
        'status.status',
        'public',
        'category.category',
    ];

    public $filterable = [
        'id',
        'question',
        'quality.rating',
        'status.status',
        'public',
        'category.category',
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

    public function institution(): BelongsToMany
    {
        return $this->belongsToMany(Institution::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
