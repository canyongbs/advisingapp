<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Query\Builder;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperFormStep
 */
#[NoPermissions]
class FormAuthentication extends BaseModel
{
    use MassPrunable;

    public function form(): BelongsTo
    {
        return $this
            ->belongsTo(Form::class);
    }

    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    public function isExpired(): bool
    {
        return $this->created_at->addDay()->isPast();
    }

    public function prunable(): Builder
    {
        return static::query()
            ->where('created_at', '<', now()->subMonth());
    }
}
