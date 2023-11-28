<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Submissible $submissible
 * @property-read SubmissibleStep $step
 */
abstract class SubmissibleField extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function step(): BelongsTo;

    abstract public function isRequired(): bool;

    abstract public function getType(): string;
}
