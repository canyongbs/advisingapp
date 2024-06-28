<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use AdvisingApp\Ai\Models\Contracts\AiFile;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiAssistantFile extends BaseModel implements AiFile, HasMedia
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

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file')
            ->singleFile();
    }
}
