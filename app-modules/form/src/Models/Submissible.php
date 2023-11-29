<?php

namespace Assist\Form\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
 * @property string $name
 * @property ?array $content
 * @property bool $embed_enabled
 * @property bool $is_wizard
 * @property ?array $allowed_domains
 * @property-read Collection<int, SubmissibleStep> $steps
 * @property-read Collection<int, SubmissibleField> $fields
 */
abstract class Submissible extends Model
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    abstract public function fields(): HasMany;

    abstract public function steps(): HasMany;

    abstract public function submissions(): HasMany;

    protected function name(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('name') ? $this->castAttribute('name', $value) : $value);
    }

    protected function content(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('content') ? $this->castAttribute('content', $value) : $value);
    }

    protected function embedEnabled(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('embed_enabled') ? $this->castAttribute('embed_enabled', $value) : $value);
    }

    protected function allowedDomains(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('allowed_domains') ? $this->castAttribute('allowed_domains', $value) : $value);
    }

    protected function isWizard(): Attribute
    {
        return Attribute::make(get: fn ($value) => $this->hasCast('is_wizard') ? $this->castAttribute('is_wizard', $value) : $value);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
