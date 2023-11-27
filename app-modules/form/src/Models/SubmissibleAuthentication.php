<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[NoPermissions]
abstract class SubmissibleAuthentication extends BaseModel
{
    use MassPrunable;

    abstract public function submissible(): BelongsTo;

    public function isExpired(): bool
    {
        return $this->created_at->addDay()->isPast();
    }

    public function prunable(): Builder
    {
        return static::query()
            ->where('created_at', '<', now()->subMonth());
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }
}
