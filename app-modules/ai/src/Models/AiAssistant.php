<?php

namespace AdvisingApp\Ai\Models;

use AdvisingApp\Ai\Enums\AiModel;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AiAssistant extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'assistant_id',
        'model',
        'name',
        'is_default',
        'description',
        'instructions',
        'knowledge',
    ];

    protected $casts = [
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
