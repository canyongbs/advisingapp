<?php

namespace AdvisingApp\Form\Models;

use AdvisingApp\Form\Database\Factories\SecretFactory;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperSecret
 */
#[NoPermissions]
class Secret extends Model
{
    /** @use HasFactory<SecretFactory> */
    use HasFactory;

    use HasUuids;
    use MassPrunable;
    use UsesTenantConnection;

    protected $fillable = [
        'value',
    ];

    protected $hidden = [
        'value',
    ];

    /**
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()
            ->whereNull('related_id')
            ->where('updated_at', '<=', now()->subDay());
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function related(): MorphTo
    {
        return $this->morphTo('related');
    }
}
