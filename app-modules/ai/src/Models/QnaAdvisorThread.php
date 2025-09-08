<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class QnaAdvisorThread extends BaseModel
{
    public $fillable = [
        'author_type',
        'author_id',
    ];

    /**
     * @return HasMany<QnaAdvisorMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(QnaAdvisorMessage::class, 'thread_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function author(): MorphTo
    {
        return $this->morphTo('author');
    }
}
