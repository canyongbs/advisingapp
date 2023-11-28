<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property bool $is_required
 * @property string $type
 * @property string $label
 * @property array $config
 * @property-read Submissible $submissible
 * @property-read SubmissibleStep $step
 */
abstract class SubmissibleField extends BaseModel
{
    abstract public function submissible(): BelongsTo;

    abstract public function step(): BelongsTo;

    protected function isRequired(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('is_required') ? $this->castAttribute('is_required', $value) : $value);
    }

    protected function type(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('type') ? $this->castAttribute('type', $value) : $value);
    }

    protected function label(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('label') ? $this->castAttribute('label', $value) : $value);
    }

    protected function config(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('config') ? $this->castAttribute('config', $value) : $value);
    }
}
