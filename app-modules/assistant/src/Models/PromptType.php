<?php

namespace AdvisingApp\Assistant\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromptType extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
    ];

    public function prompts(): HasMany
    {
        return $this->hasMany(Prompt::class, 'type_id');
    }
}
