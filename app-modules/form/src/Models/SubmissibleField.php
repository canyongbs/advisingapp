<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Submissible $submissible
 * @property SubmissibleStep $step
 */
abstract class SubmissibleField extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function step(): BelongsTo;
}
