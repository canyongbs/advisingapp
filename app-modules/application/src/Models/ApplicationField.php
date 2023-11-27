<?php

namespace Assist\Application\Models;

use Assist\Form\Models\SubmissibleField;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperApplicationField
 */
class ApplicationField extends SubmissibleField
{
    protected $fillable = [
        'config',
        'label',
        'type',
        'is_required',
        'application_id',
    ];

    protected $casts = [
        'config' => 'array',
        'is_required' => 'bool',
    ];

    public function submissible(): BelongsTo
    {
        return $this
            ->belongsTo(Application::class, 'application_id');
    }

    public function step(): BelongsTo
    {
        return $this
            ->belongsTo(ApplicationStep::class, 'step_id');
    }
}
