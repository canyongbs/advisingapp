<?php

namespace AdvisingApp\Application\Models;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperApplicationSubmissionsChecklistItem
 */
class ApplicationSubmissionsChecklistItem extends BaseModel
{
    protected $fillable = [
        'application_submission_id',
        'title',
        'is_checked',
        'created_by',
        'completed_by',
        'completed_date',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'completed_date' => 'datetime',
    ];

    /**
     * @return BelongsTo<ApplicationSubmission, $this>
     */
    public function applicationSubmission(): BelongsTo
    {
        return $this->belongsTo(ApplicationSubmission::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
