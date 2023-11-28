<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property-read Submissible $submissible
 * @property-read Collection<int, SubmissibleField> $fields
 * @property-read Student|Prospect|null $author
 */
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
