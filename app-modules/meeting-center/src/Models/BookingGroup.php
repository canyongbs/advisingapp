<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\MeetingCenter\Database\Factories\BookingGroupFactory;
use AdvisingApp\Team\Models\Team;
use App\Models\BaseModel;
use App\Models\User;
use CanyonGBS\Common\Models\Concerns\HasUserSaveTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperBookingGroup
 */
class BookingGroup extends BaseModel implements Auditable
{
    /** @use HasFactory<BookingGroupFactory> */
    use HasFactory;

    use AuditableTrait;
    use HasUserSaveTracking;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'book_with',
        'default_appointment_duration',
        'is_default_appointment_buffer_enabled',
        'default_appointment_buffer_before_duration',
        'default_appointment_buffer_after_duration',
        'available_appointment_hours',
    ];

    protected $casts = [
        'default_appointment_duration' => 'integer',
        'is_default_appointment_buffer_enabled' => 'boolean',
        'default_appointment_buffer_before_duration' => 'integer',
        'default_appointment_buffer_after_duration' => 'integer',
        'available_appointment_hours' => 'array',
    ];

    /**
    * @return BelongsToMany<User, $this, covariant BookingGroupUser>
    */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'booking_group_users'
        )
            ->using(BookingGroupUser::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant BookingGroupTeam>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Team::class,
            table: 'booking_group_teams'
        )
            ->using(BookingGroupTeam::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<BookingGroupAppointment, $this>
     */
    public function bookingGroupAppointments(): HasMany
    {
        return $this->hasMany(BookingGroupAppointment::class);
    }

    /**
     * @return Collection<int, User>
     */
    public function allMembers(): Collection
    {
        $directUsers = $this->users()->get();

        $teamIds = $this->teams()->pluck('teams.id');

        $teamMembers = $teamIds->isNotEmpty()
            ? User::query()->whereIn('team_id', $teamIds)->get()
            : new Collection();

        return $directUsers->merge($teamMembers)->unique('id')->values();
    }
}
