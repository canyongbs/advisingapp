<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QnaAdvisorQuestion extends BaseModel
{
    use SoftDeletes;

    protected $fillable = ['question', 'answer', 'category_id'];

    /**
     * @return BelongsTo<QnAAdvisorCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QnAAdvisorCategory::class, 'category_id');
    }
}
