<?php

namespace Assist\Form\Models;

use App\Models\BaseModel;
use Assist\Form\Enums\Rounding;
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
        'is_wizard',
        'primary_color',
        'rounding',
    ];

    protected $casts = [
        'embed_enabled' => 'boolean',
        'allowed_domains' => 'array',
        'is_wizard' => 'boolean',
        'rounding' => Rounding::class,
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(FormStep::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
