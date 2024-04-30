<?php

namespace AdvisingApp\Assistant\Models;

use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Assistant\Enums\AiAssistantType;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperAiAssistant
 */
class AiAssistant extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'assistant_id',
        'description',
        'instructions',
        'knowledge',
        'name',
        'type',
    ];

    protected $casts = [
        'type' => AiAssistantType::class,
    ];

    public function assistantChats(): HasMany
    {
        return $this->hasMany(AssistantChat::class);
    }

    public function scopeDefault(Builder $query): void
    {
        $query->where('type', AiAssistantType::Default);
    }
}
