<?php

namespace AdvisingApp\Assistant\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\Assistant\Enums\AiAssistantType;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiAssistant extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'assistant_id',
        'description',
        'instructions',
        'knowledge',
        'name',
        'profile_image',
        'type',
    ];

    protected $casts = [
        'type' => AiAssistantType::class,
    ];

    public function assistantChats(): HasMany
    {
        return $this->hasMany(AssistantChat::class);
    }
}
