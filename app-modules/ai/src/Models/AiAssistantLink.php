<?php

namespace AdvisingApp\Ai\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class AiAssistantLink extends BaseModel implements Auditable
{
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'ai_assistant_id',
        'parsing_results',
        'url',
    ];

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class);
    }
}
