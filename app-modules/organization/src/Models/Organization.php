<?php

namespace AdvisingApp\Organization\Models;

use AdvisingApp\Organization\Database\Factories\OrganizationFactory;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @mixin IdeHelperOrganization
 */
class Organization extends Model
{
    /** @use HasFactory<OrganizationFactory> */
    use HasFactory;

    use HasUuids;
    use UsesTenantConnection;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'website',
        'description',
        'address',
        'city',
        'state',
        'postalcode',
        'country',
        'domains',
    ];

    protected $casts = [
        'domains' => 'array',
    ];

    /**
     * @return HasMany<Prospect, $this>
     */
    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class);
    }
}
