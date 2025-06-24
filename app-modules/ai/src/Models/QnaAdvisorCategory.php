<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperQnaAdvisorCategory
 */
class QnaAdvisorCategory extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'qna_advisor_id',
    ];

    /**
     * @return BelongsTo<QnaAdvisor, $this>
     */
    public function qnaAdvisor(): BelongsTo
    {
        return $this->belongsTo(QnaAdvisor::class, 'qna_advisor_id');
    }

    /**
     * @return HasMany<QnaAdvisorQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QnaAdvisorQuestion::class, 'category_id');
    }
}
