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

namespace AdvisingApp\StudentDataModel\Models;

use AdvisingApp\Alert\Models\StudentAlert;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\BasicNeeds\Models\BasicNeedsProgram;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Engagement\Models\Concerns\HasManyMorphedEngagementResponses;
use AdvisingApp\Engagement\Models\Concerns\HasManyMorphedEngagements;
use AdvisingApp\Engagement\Models\EngagementFile;
use AdvisingApp\Engagement\Models\EngagementFileEntities;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Group\Models\GroupSubject;
use AdvisingApp\Interaction\Models\Concerns\HasManyMorphedInteractions;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\Notification\Models\Concerns\HasSubscriptions;
use AdvisingApp\Notification\Models\Concerns\NotifiableViaSms;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Notification\Models\Contracts\Subscribable;
use AdvisingApp\Notification\Models\Subscription;
use AdvisingApp\Pipeline\Models\EducatablePipelineStage;
use AdvisingApp\Pipeline\Models\Pipeline;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Database\Factories\StudentFactory;
use AdvisingApp\StudentDataModel\Enums\EmailHealthStatus;
use AdvisingApp\StudentDataModel\Filament\Resources\Students\StudentResource;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Scopes\RetentionCrmRestrictionScope;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Timeline\Models\Contracts\HasFilamentResource;
use AdvisingApp\Timeline\Models\Timeline;
use App\Enums\TagType;
use App\Models\Authenticatable;
use App\Models\Scopes\HasLicense;
use App\Models\Tag;
use App\Models\Taggable;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as BaseAuthenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property string $display_name
 * @property string $mobile
 *
 * @mixin IdeHelperStudent
 */
#[ScopedBy([RetentionCrmRestrictionScope::class])]
class Student extends BaseAuthenticatable implements Auditable, Subscribable, Educatable, HasFilamentResource, CanBeNotified
{
    use SoftDeletes;
    use HasApiTokens;
    use AuditableTrait;

    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    use Notifiable;
    use HasManyMorphedEngagements;
    use HasManyMorphedEngagementResponses;
    use HasManyMorphedInteractions;
    use HasSubscriptions;
    use NotifiableViaSms;
    use UsesTenantConnection;
    use HasRelationships;

    protected $table = 'students';

    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'sisid',
        'otherid',
        'first',
        'last',
        'full_name',
        'preferred',
        'birthdate',
        'hsgrad',
        'gender',
        'dual',
        'ferpa',
        'dfw',
        'sap',
        'holds',
        'firstgen',
        'ethnicity',
        'lastlmslogin',
        'primary_email_id',
        'primary_phone_id',
        'primary_address_id',
        'created_at_source',
        'updated_at_source',
        'athletics_status',
        'athletic_details',
        'standing',
        'sis_category',
    ];

    protected $casts = [
        'sisid' => 'string',
        'created_at_source' => 'datetime',
        'updated_at_source' => 'datetime',
        'birthdate' => 'date',
        'dfw' => 'date',
        'dual' => 'boolean',
        'ferpa' => 'boolean',
        'sap' => 'boolean',
        'firstgen' => 'boolean',
        'hsgrad' => 'date',
    ];

    public function identifier(): string
    {
        return $this->sisid;
    }

    public static function displayFirstNameKey(): string
    {
        return 'first';
    }

    public static function displayLastNameKey(): string
    {
        return 'last';
    }

    public static function displayNameKey(): string
    {
        return 'full_name';
    }

    public static function displayEmailKey(): string
    {
        return 'email';
    }

    public static function displayPreferredNameKey(): string
    {
        return 'preferred';
    }

    /**
     * @return MorphMany<CaseModel, $this>
     */
    public function cases(): MorphMany
    {
        return $this->morphMany(
            related: CaseModel::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'sisid'
        );
    }

    /**
     * @return HasOne<Enrollment, $this>
     */
    public function firstEnrollmentTerm(): HasOne
    {
        return $this->enrollments()
            ->one()
            ->ofMany([
                'start_date' => 'min',
            ], function (Builder $query) {
                $query->whereNotNull('semester_code')
                    ->whereNotNull('start_date');
            });
    }

    /**
     * @return HasOne<Enrollment, $this>
     */
    public function mostRecentEnrollmentTerm(): HasOne
    {
        return $this->enrollments()
            ->one()
            ->ofMany([
                'start_date' => 'max',
            ], function (Builder $query) {
                $query->whereNotNull('semester_code')
                    ->whereNotNull('start_date');
            });
    }

    /**
     * @return MorphToMany<EngagementFile, $this, covariant EngagementFileEntities>
     */
    public function engagementFiles(): MorphToMany
    {
        return $this->morphToMany(
            related: EngagementFile::class,
            name: 'entity',
            table: 'engagement_file_entities',
            foreignPivotKey: 'entity_id',
            relatedPivotKey: 'engagement_file_id',
            relation: 'engagementFiles',
        )
            ->using(EngagementFileEntities::class)
            ->withTimestamps();
    }

    /**
     * @return MorphMany<Task, $this>
     */
    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'concern');
    }

    /**
     * @return MorphMany<Concern, $this>
     */
    public function concerns(): MorphMany
    {
        return $this->morphMany(Concern::class, 'concern');
    }

    /**
     * @return HasMany<Program, $this>
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class, 'sisid', 'sisid');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'sisid', 'sisid');
    }

    /**
     * @return HasMany<Hold, $this>
     */
    public function holds(): HasMany
    {
        return $this->hasMany(Hold::class, 'sisid', 'sisid');
    }

    /**
     * @return MorphMany<FormSubmission, $this>
     */
    public function formSubmissions(): MorphMany
    {
        return $this->morphMany(FormSubmission::class, 'author');
    }

    /**
     * @return MorphMany<ApplicationSubmission, $this>
     */
    public function applicationSubmissions(): MorphMany
    {
        return $this->morphMany(ApplicationSubmission::class, 'author');
    }

    /**
     * @return MorphToMany<User, $this, covariant CareTeam>
     */
    public function careTeam(): MorphToMany
    {
        return $this->morphToMany(
            related: User::class,
            name: 'educatable',
            table: 'care_teams',
        )
            ->using(CareTeam::class)
            ->withPivot(['id', 'care_team_role_id'])
            ->withTimestamps()
            ->tap(new HasLicense($this->getLicenseType()));
    }

    /**
     * @return MorphToMany<User, $this, covariant Subscription>
     */
    public function subscribedUsers(): MorphToMany
    {
        return $this->morphToMany(
            related: User::class,
            name: 'subscribable',
            table: 'subscriptions',
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps()
            ->tap(new HasLicense($this->getLicenseType()));
    }

    /**
     * @return HasManyThrough<EventAttendee, StudentEmailAddress, $this>
     */
    public function eventAttendeeRecords(): HasManyThrough
    {
        return $this->hasManyThrough(
            related: EventAttendee::class,
            through: StudentEmailAddress::class,
            firstKey: 'sisid',
            secondKey: 'email',
            localKey: 'sisid',
            secondLocalKey: 'address',
        );
    }

    /**
     * @return MorphMany<GroupSubject, $this>
     */
    public function groupSubjects(): MorphMany
    {
        return $this->morphMany(
            related: GroupSubject::class,
            name: 'subject',
            type: 'subject_type',
            id: 'subject_id',
            localKey: 'sisid'
        );
    }

    public static function filamentResource(): string
    {
        return StudentResource::class;
    }

    /**
     * @return HasManyDeep<Model, $this>
     */
    public function concernHistories(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->concerns(), (new Concern())->histories());
    }

    public function taskHistories(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->tasks(), (new Task())->histories());
    }

    /**
     * @return HasMany<Prospect, $this>
     */
    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'student_id');
    }

    public static function getLicenseType(): LicenseType
    {
        return LicenseType::RetentionCrm;
    }

    public function timeline(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'entity');
    }

    /**
     * @return MorphToMany<BasicNeedsProgram, $this>
     */
    public function basicNeedsPrograms(): MorphToMany
    {
        return $this->morphToMany(
            related: BasicNeedsProgram::class,
            name: 'program_participants',
            table: 'program_participants',
            foreignPivotKey: 'program_participants_id',
            relatedPivotKey: 'basic_needs_program_id'
        )->withTimestamps();
    }

    /**
     * @return HasMany<StudentAddress, $this>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(StudentAddress::class, 'sisid', 'sisid')->orderBy('order');
    }

    /**
     * @return HasMany<StudentEmailAddress, $this>
     */
    public function emailAddresses(): HasMany
    {
        return $this->hasMany(StudentEmailAddress::class, 'sisid', 'sisid')->orderBy('order');
    }

    /**
     * @return HasMany<StudentPhoneNumber, $this>
     */
    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(StudentPhoneNumber::class, 'sisid', 'sisid')->orderBy('order');
    }

    /**
     * @return BelongsTo<StudentEmailAddress, $this>
     */
    public function primaryEmailAddress(): BelongsTo
    {
        return $this->belongsTo(StudentEmailAddress::class, 'primary_email_id');
    }

    /**
     * @return BelongsTo<StudentPhoneNumber, $this>
     */
    public function primaryPhoneNumber(): BelongsTo
    {
        return $this->belongsTo(StudentPhoneNumber::class, 'primary_phone_id');
    }

    public function primaryAddress()
    {
        return $this->belongsTo(StudentAddress::class, 'primary_address_id');
    }

    public function additionalEmailAddresses(): HasMany
    {
        return $this->emailAddresses()->whereKeyNot($this->primary_email_id);
    }

    public function additionalPhoneNumbers(): HasMany
    {
        return $this->phoneNumbers()->whereKeyNot($this->primary_phone_id);
    }

    public function additionalAddresses(): HasMany
    {
        return $this->addresses()->whereKeyNot($this->primary_address_id);
    }

    /**
     * @return HasMany<StudentAlert, $this>
     */
    public function studentAlerts(): HasMany
    {
        return $this->hasMany(StudentAlert::class, 'sisid', 'sisid');
    }

    public static function getLabel(): string
    {
        return 'student';
    }

    public function canReceiveEmail(): bool
    {
        return $this->primaryEmailAddress?->getHealthStatus() === EmailHealthStatus::Healthy;
    }

    public function canReceiveSms(): bool
    {
        return $this->primaryPhoneNumber?->can_receive_sms && (! $this->primaryPhoneNumber->smsOptOut()->exists());
    }

    /**
     * @return MorphToMany<Tag, $this, covariant Taggable>
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(
            related: Tag::class,
            name: 'taggable',
            table: 'taggables',
            foreignPivotKey: 'taggable_id',
            relatedPivotKey: 'tag_id',
        )
            ->using(Taggable::class)
            ->withPivot(['tag_id'])
            ->withTimestamps()
            ->where('type', TagType::Student);
    }

    /**
     * Route notifications for the mail channel.
     *
     * @return array<string, string>|string|null
     */
    public function routeNotificationForMail(Notification $notification): ?string
    {
        return $this->primaryEmailAddress?->address;
    }

    /**
     * @return MorphToMany<Pipeline, $this, EducatablePipelineStage>
     */
    public function educatablePipelineStages(): MorphToMany
    {
        return $this->morphToMany(
            related: Pipeline::class,
            name: 'educatable',
            table: 'educatable_pipeline_stages',
            foreignPivotKey: 'educatable_id',
            relatedPivotKey: 'pipeline_id',
        )
            ->using(EducatablePipelineStage::class)
            ->withPivot(['pipeline_stage_id'])
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->guard('web')->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->guard('web')->user();

            if (! $user->hasLicense(Student::getLicenseType())) {
                $builder->whereRaw('1 = 0');
            }
        });
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes[$this->displayNameKey()],
        );
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): ?string {
                return $this->primaryAddress?->full;
            }
        );
    }

    /**
     * @param  string  $childType
     */
    protected function childRouteBindingRelationshipName($childType): string
    {
        return match ($childType) {
            'studentEmailAddress' => 'emailAddresses',
            'studentPhoneNumber' => 'phoneNumbers',
            default => parent::childRouteBindingRelationshipName($childType),
        };
    }
}
