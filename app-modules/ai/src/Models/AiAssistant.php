<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use AdvisingApp\Ai\Enums\AiModel;
use Spatie\MediaLibrary\HasMedia;
use AdvisingApp\Ai\Enums\AiApplication;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiAssistant extends BaseModel implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'assistant_id',
        'name',
        'application',
        'model',
        'is_default',
        'description',
        'instructions',
        'knowledge',
    ];

    protected $casts = [
        'application' => AiApplication::class,
        'is_default' => 'bool',
        'model' => AiModel::class,
    ];

    public function threads(): HasMany
    {
        return $this->hasMany(AiThread::class, 'assistant_id');
    }

    public function upvotes(): HasMany
    {
        return $this->hasMany(AiAssistantUpvote::class, 'assistant_id');
    }
}
