<?php

namespace Assist\Application\Models;

use Assist\Form\Enums\Rounding;
use Assist\Form\Models\Submissible;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperApplication
 */
class Application extends Submissible
{
    protected $fillable = [
        'name',
        'description',
        'embed_enabled',
        'allowed_domains',
        'is_wizard',
        'primary_color',
        'rounding',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
        'embed_enabled' => 'boolean',
        'allowed_domains' => 'array',
        'is_wizard' => 'boolean',
        'rounding' => Rounding::class,
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(ApplicationField::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(ApplicationStep::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ApplicationSubmission::class);
    }

    protected function isWizard(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
        );
    }

    protected function content(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
        );
    }
}
