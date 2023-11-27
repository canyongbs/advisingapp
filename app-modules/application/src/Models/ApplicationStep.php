<?php

namespace Assist\Application\Models;

use Assist\Form\Models\SubmissibleStep;
use App\Models\Attributes\NoPermissions;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[NoPermissions]
class ApplicationStep extends SubmissibleStep
{
    protected $fillable = [
        'label',
        'content',
        'sort',
    ];

    protected $casts = [
        'content' => 'array',
        'sort' => 'integer',
    ];

    public function submissible(): BelongsTo
    {
        return $this
            ->belongsTo(Application::class, 'application_id');
    }

    public function fields(): HasMany
    {
        return $this
            ->hasMany(ApplicationField::class, 'step_id');
    }
}
