<?php

namespace Assist\Form\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

abstract class Submissible extends Model
{
    use HasFactory;
    use DefinesPermissions;
    use HasUuids;

    abstract public function fields(): HasMany;

    abstract public function steps(): HasMany;

    abstract public function submissions(): HasMany;

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
