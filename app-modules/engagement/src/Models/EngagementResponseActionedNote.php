<?php

namespace AdvisingApp\Engagement\Models;

use AdvisingApp\Engagement\Database\Factories\EngagementResponseActionedNoteFactory;
use CanyonGBS\Common\Models\Concerns\HasUserSaveTracking;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Workbench\App\Models\User;

class EngagementResponseActionedNote extends Model
{
    /** @use HasFactory<EngagementResponseActionedNoteFactory> */
    use HasFactory;

    use HasUuids;
    use HasUserSaveTracking;

    protected $fillable = [
        'engagement_response_id',
        'created_by_id',
        'note',
    ];

    /**
     * Get the engagement response associated with the actioned note.
     *
     * @return BelongsTo<EngagementResponse, $this>
     */
    public function engagementResponse(): BelongsTo
    {
        return $this->belongsTo(EngagementResponse::class, 'engagement_response_id');
    }

    public function getActionedNoteTooltip(): string
    {
        /** @var User|null $user */
        $user = $this->createdBy;
        $date = $this->created_at?->format('m-d-Y - g:i A');
        $name = $user->name ?? 'Unknown User';

        return "{$date} - {$name} marked this message as actioned with the following note: {$this->note}";
    }
}
