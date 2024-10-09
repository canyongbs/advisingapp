<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace App\Models;

use Filament\Panel;
use DateTimeInterface;
use Illuminate\Support\Arr;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Team\Models\Team;
use Spatie\MediaLibrary\HasMedia;
use App\Support\HasAdvancedFilter;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Team\Models\TeamUser;
use AdvisingApp\Segment\Models\Segment;
use App\Filament\Resources\UserResource;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use AdvisingApp\Ai\Models\AiThreadFolder;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Authorization\Models\Role;
use Lab404\Impersonate\Models\Impersonate;
use Filament\Models\Contracts\FilamentUser;
use Spatie\MediaLibrary\InteractsWithMedia;
use AdvisingApp\Ai\Models\AiAssistantUpvote;
use AdvisingApp\Authorization\Models\License;
use Illuminate\Database\Eloquent\SoftDeletes;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\StudentDataModel\Models\Student;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use AdvisingApp\Notification\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasOne;
use AdvisingApp\Consent\Models\Concerns\CanConsent;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use AdvisingApp\ServiceManagement\Models\ChangeRequestType;
use AdvisingApp\InAppCommunication\Models\TwilioConversation;
use AdvisingApp\Engagement\Models\Concerns\HasManyEngagements;
use AdvisingApp\Notification\Models\Concerns\NotifiableViaSms;
use AdvisingApp\Timeline\Models\Contracts\HasFilamentResource;
use AdvisingApp\ServiceManagement\Models\ChangeRequestResponse;
use AdvisingApp\InAppCommunication\Models\TwilioConversationUser;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Notification\Models\Contracts\NotifiableInterface;
use AdvisingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AdvisingApp\Engagement\Models\Concerns\HasManyEngagementBatches;
use AdvisingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AdvisingApp\MultifactorAuthentication\Traits\MultifactorAuthenticatable;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements HasLocalePreference, FilamentUser, Auditable, HasMedia, HasAvatar, NotifiableInterface, HasFilamentResource
{
    use HasFactory;
    use HasAdvancedFilter;
    use Notifiable;
    use SoftDeletes;
    use HasRelationships;
    use HasUuids;
    use AuditableTrait;
    use HasManyEngagements;
    use HasManyEngagementBatches;
    use CanConsent;
    use Impersonate;
    use InteractsWithMedia;
    use NotifiableViaSms;
    use MultifactorAuthenticatable;

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_bio_visible_on_profile' => 'boolean',
        'are_pronouns_visible_on_profile' => 'boolean',
        'are_teams_visible_on_profile' => 'boolean',
        'default_assistant_chat_folders_created' => 'boolean',
        'is_division_visible_on_profile' => 'boolean',
        'email_verified_at' => 'datetime',
        'has_enabled_public_profile' => 'boolean',
        'office_hours_are_enabled' => 'boolean',
        'appointments_are_restricted_to_existing_students' => 'boolean',
        'office_hours' => 'array',
        'out_of_office_is_enabled' => 'boolean',
        'out_of_office_starts_at' => 'datetime',
        'out_of_office_ends_at' => 'datetime',
        'is_email_visible_on_profile' => 'boolean',
        'is_phone_number_visible_on_profile' => 'boolean',
        'working_hours_are_enabled' => 'boolean',
        'are_working_hours_visible_on_profile' => 'boolean',
        'working_hours' => 'array',
        'last_chat_ping_at' => 'immutable_datetime',
    ];

    protected $fillable = [
        'emplid',
        'name',
        'email',
        'password',
        'locale',
        'type',
        'is_external',
        'bio',
        'is_bio_visible_on_profile',
        'are_pronouns_visible_on_profile',
        'default_assistant_chat_folders_created',
        'avatar_url',
        'are_teams_visible_on_profile',
        'timezone',
        'is_division_visible_on_profile',
        'has_enabled_public_profile',
        'public_profile_slug',
        'office_hours_are_enabled',
        'appointments_are_restricted_to_existing_students',
        'office_hours',
        'out_of_office_is_enabled',
        'out_of_office_starts_at',
        'out_of_office_ends_at',
        'is_email_visible_on_profile',
        'phone_number',
        'is_phone_number_visible_on_profile',
        'working_hours_are_enabled',
        'are_working_hours_visible_on_profile',
        'working_hours',
        'job_title',
        'last_chat_ping_at',
        'is_branding_bar_dismissed',
    ];

    public $orderable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'locale',
    ];

    public $filterable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'roles.title',
        'locale',
    ];

    public function defaultAssistantChatFoldersHaveBeenCreated(): bool
    {
        return $this->default_assistant_chat_folders_created;
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(TwilioConversation::class, 'twilio_conversation_user', 'user_id', 'conversation_sid')
            ->withPivot([
                'participant_sid',
                'is_channel_manager',
                'is_pinned',
                'notification_preference',
                'first_unread_message_sid',
                'first_unread_message_at',
                'last_unread_message_content',
                'last_read_at',
                'unread_messages_count',
            ])
            ->withTimestamps()
            ->as('participant')
            ->using(TwilioConversationUser::class);
    }

    public function segments(): HasMany
    {
        return $this->hasMany(Segment::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function prospectSubscriptions(): MorphToMany
    {
        return $this->morphedByMany(
            related: Prospect::class,
            name: 'subscribable',
            table: 'subscriptions'
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function studentSubscriptions(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'subscribable',
            table: 'subscriptions'
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function studentAlerts(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->studentSubscriptions(), (new Student())->alerts());
    }

    public function prospectAlerts(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->prospectSubscriptions(), (new Prospect())->alerts());
    }

    public function prospectCareTeams(): MorphToMany
    {
        return $this->morphedByMany(
            related: Prospect::class,
            name: 'educatable',
            table: 'care_teams'
        )
            ->using(CareTeam::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function studentCareTeams(): MorphToMany
    {
        return $this->morphedByMany(
            related: Student::class,
            name: 'educatable',
            table: 'care_teams'
        )
            ->using(CareTeam::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function careTeams(): HasMany
    {
        return $this->hasMany(CareTeam::class);
    }

    public function permissionsFromRoles(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    }

    public function serviceRequestAssignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class)
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    public function serviceRequests(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->serviceRequestAssignments(), (new ServiceRequestAssignment())->serviceRequest());
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'created_by');
    }

    public function changeRequestResponses(): HasMany
    {
        return $this->hasMany(ChangeRequestResponse::class);
    }

    public function changeRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(ChangeRequestType::class);
    }

    public function getIsAdminAttribute()
    {
        return $this->roles()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->exists();
    }

    public function scopeAdmins()
    {
        return $this->whereHas('roles', fn ($q) => $q->where('title', 'Admin'));
    }

    public function pronouns(): BelongsTo
    {
        return $this->belongsTo(Pronouns::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    public function aiThreads(): HasMany
    {
        return $this->hasMany(AiThread::class);
    }

    public function aiThreadFolders(): HasMany
    {
        return $this->hasMany(AiThreadFolder::class);
    }

    public function aiAssistantUpvotes(): HasMany
    {
        return $this->hasMany(AiAssistantUpvote::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function teams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'team_user', 'user_id', 'team_id')
            ->using(TeamUser::class)
            ->withTimestamps();
    }

    public function calendar(): HasOne
    {
        return $this->hasOne(Calendar::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return $this->can('authorization.impersonate');
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->isSuperAdmin();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ?: $this->getFirstTemporaryUrl(now()->addMinutes(5), 'avatar', 'avatar-height-250px');
    }

    /**
     * @param LicenseType | string | array<LicenseType | string> | null $type
     */
    public function hasLicense(LicenseType | string | array | null $type): bool
    {
        if (blank($type)) {
            return true;
        }

        foreach (Arr::wrap($type) as $type) {
            if (! ($type instanceof LicenseType)) {
                $type = LicenseType::from($type);
            }

            if ($this->licenses->doesntContain('type', $type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param LicenseType | string | array<LicenseType | string> | null $type
     */
    public function hasAnyLicense(LicenseType | string | array | null $type): bool
    {
        if (blank($type)) {
            return true;
        }

        foreach (Arr::wrap($type) as $type) {
            if (! ($type instanceof LicenseType)) {
                $type = LicenseType::from($type);
            }

            if ($this->licenses->contains('type', $type)) {
                return true;
            }
        }

        return false;
    }

    public static function filamentResource(): string
    {
        return UserResource::class;
    }

    public function grantLicense(LicenseType $type): bool
    {
        if ($this->hasLicense($type)) {
            return false;
        }

        return cache()
            ->lock('licenses', 5)
            ->get(function () use ($type) {
                if (! $type->hasAvailableLicenses()) {
                    return false;
                }

                return (bool) $this->licenses()->create(['type' => $type]);
            });
    }

    public function revokeLicense(LicenseType $type): bool
    {
        return (bool) $this->licenses()->where('type', $type)->get()->each->delete();
    }

    public function getDynamicContext(): string
    {
        $context = "My name is \"{$this->name}\"";

        if ($this->job_title) {
            $context .= " and my job title is \"{$this->job_title}\"";
        }

        return "{$context}. When you respond please use this information about me to tailor your response. You should refer to me by my name and remember what my name and job title are, using it in your responses when appropriate.";
    }

    public function routeNotificationForSms(): string
    {
        return $this->phone_number;
    }

    public function assignTeam($teamId)
    {
        // Remove the current team if exists
        $this->teams()->detach();

        // Assign the new team
        $this->teams()->attach($teamId);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
