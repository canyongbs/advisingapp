<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

abstract class Submission extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function fields(): BelongsToMany;

    public function author(): MorphTo
    {
        return $this
            ->morphTo('author');
    }
}
