<?php

namespace AdvisingApp\Assistant\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prompt extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'prompt',
        'type_id',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(PromptType::class);
    }
}
