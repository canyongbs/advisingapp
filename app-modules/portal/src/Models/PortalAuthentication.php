<?php

namespace AdvisingApp\Portal\Models;

use App\Models\BaseModel;
use AdvisingApp\Portal\Enums\PortalType;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[NoPermissions]
class PortalAuthentication extends BaseModel
{
    use MassPrunable;

    protected $casts = [
        'portal_type' => PortalType::class,
    ];

    public function isExpired(): bool
    {
        return $this->created_at->addDay()->isPast();
    }

    public function prunable(): Builder
    {
        return static::query()
            ->where('created_at', '<', now()->subMonth());
    }

    public function educatable(): MorphTo
    {
        return $this->morphTo();
    }
}
