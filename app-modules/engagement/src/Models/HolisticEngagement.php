<?php

namespace AdvisingApp\Engagement\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class HolisticEngagement extends Model
{
    use HasUuids;

    protected $table = 'holistic_engagements';

    protected $primaryKey = 'record_id';

    protected $casts = [
        'record_sortable_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function concern(): MorphTo
    {
        return $this->morphTo(
            name: 'concern',
            type: 'concern_type',
            id: 'concern_id',
            // ownerKey: 'id',
        );
    }

    /**
     * @return MorphTo<contravariant Engagement|EngagementResponse, $this>
     */
    public function record(): MorphTo
    {
        return $this->morphTo(
            name: 'record',
            type: 'record_type',
            id: 'record_id',
            ownerKey: 'id',
        );
    }
}
