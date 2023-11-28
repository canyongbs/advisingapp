<?php

namespace Assist\Form\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

/**
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

    abstract public function isWizard(): bool;

    abstract public function getContent(): ?array;

    abstract public function isEmbedEnabled(): bool;

    abstract public function getAllowedDomains(): ?array;

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
