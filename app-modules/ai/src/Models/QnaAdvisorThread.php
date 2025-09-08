<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QnaAdvisorThread extends BaseModel
{
    /**
     * @return HasMany<QnaAdvisorMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(QnaAdvisorMessage::class, 'thread_id');
    }
}
