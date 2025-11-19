<?php

namespace AdvisingApp\GroupAppointment\Models;

use AdvisingApp\Team\Models\Team;
use App\Models\User;
use CanyonGBS\Common\Models\Concerns\HasUserSaveTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BookingGroup extends Model
{
    use HasUserSaveTracking;

    protected $fillable = [
        'name',
        'description',
        'is_confidential',
        'created_by_id',
        'last_updated_by_id',
    ];

    /**
     * @return HasOne<Team, $this>
     */
    public function team(): HasOne
    {
        return $this->hasOne(Team::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps()
            ->withPivot('id');
    }
}
