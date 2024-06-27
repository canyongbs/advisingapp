<?php

namespace AdvisingApp\IntegrationOpenAi\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AzureOpenAiVectorStore extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'vector_store_id',
        'vector_storable_id',
        'vector_storable_type',
    ];

    public function vectorStorable(): MorphTo
    {
        return $this->morphTo();
    }
}
