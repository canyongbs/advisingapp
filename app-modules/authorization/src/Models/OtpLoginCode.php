<?php

namespace AdvisingApp\Authorization\Models;

use AdvisingApp\Authorization\Database\Factories\OtpLoginCodeFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperOtpLoginCode
 */
class OtpLoginCode extends Model
{
    /** @use HasFactory<OtpLoginCodeFactory> */
    use HasFactory;

    use HasUuids;
    use UsesTenantConnection;
    use MassPrunable;

    protected $fillable = [];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return Builder<OtpLoginCode>
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subMinutes(15))
            ->orWhereNotNull('used_at');
    }
}
