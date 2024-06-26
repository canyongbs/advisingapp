<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAssistantFile extends BaseModel
{
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'file_id',
        'message_id',
        'mime_type',
        'name',
        'temporary_url',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('files');
    }
}
