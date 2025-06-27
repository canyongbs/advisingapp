<?php

namespace AdvisingApp\IntegrationOpenAi\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenAiVectorStore extends BaseModel
{
    use SoftDeletes;

    public $filled = [
        'deployment_hash',
        'ready_until',
        'vector_store_id',
        'vector_store_file_id',
    ];

    protected $casts = [
        'ready_until' => 'immutable_datetime',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function file(): MorphTo
    {
        return $this->morphTo('file');
    }
}
