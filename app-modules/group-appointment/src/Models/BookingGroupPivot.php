<?php

namespace AdvisingApp\GroupAppointment\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperBookingGroupPivot
 */
class BookingGroupPivot extends MorphPivot
{
    use HasUuids;

    protected $fillable = [
        'booking_group_id',
        'user_id',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }
}
