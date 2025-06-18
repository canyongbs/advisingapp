<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QnAAdvisorEmbed extends BaseModel
{
    protected $fillable = [
        'is_enabled',
        'qn_a_advisor_id',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * @return BelongsTo<QnAAdvisor, $this>
     */
    public function qnAAdvisor(): BelongsTo
    {
        return $this->belongsTo(QnAAdvisor::class, 'qn_a_advisor_id');
    }
}
