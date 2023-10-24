<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperForm
 */
class Form extends BaseModel
{
    protected $fillable = [
        'name',
        'description',
        'embed_enabled',
        'allowed_domains',
    ];

    protected $casts = [
        'embed_enabled' => 'boolean',
        'allowed_domains' => 'array',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
