<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Prospect\Models;

use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Assist\Task\Models\Task;
use Assist\Alert\Models\Alert;
use Illuminate\Support\Collection;
use Assist\CareTeam\Models\CareTeam;
use Assist\Form\Models\FormSubmission;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use OpenSearch\ScoutDriverPlus\Searchable;
use Assist\Engagement\Models\EngagementFile;
use Assist\Notifications\Models\Subscription;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Assist\Engagement\Models\EngagementFileEntities;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Models\Contracts\Subscribable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Assist\Prospect\Filament\Resources\ProspectResource;
use Assist\Timeline\Models\Contracts\HasFilamentResource;
use Assist\Notifications\Models\Concerns\HasSubscriptions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;
use Assist\Engagement\Models\Concerns\HasManyMorphedEngagements;
use Assist\Interaction\Models\Concerns\HasManyMorphedInteractions;
use Assist\Engagement\Models\Concerns\HasManyMorphedEngagementResponses;

/**
 * @property string $display_name
 *
 * @mixin IdeHelperProspect
 */
class Prospect extends BaseModel implements Auditable, Subscribable, Educatable, HasFilamentResource
{
    use HasUuids;
    use SoftDeletes;
    use AuditableTrait;
    use Notifiable;
    use HasManyMorphedEngagements;
    use HasManyMorphedEngagementResponses;
    use HasManyMorphedInteractions;
    use HasSubscriptions;
    use Searchable;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'preferred',
        'description',
        'email',
        'email_2',
        'mobile',
        'sms_opt_out',
        'email_bounce',
        'status_id',
        'source_id',
        'phone',
        'address',
        'address_2',
        'birthdate',
        'hsgrad',
        'assigned_to_id',
        'created_by_id',
    ];

    protected $casts = [
        'sms_opt_out' => 'boolean',
        'email_bounce' => 'boolean',
    ];

    public function searchableAs(): string
    {
        return config('scout.prefix') . 'prospects';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => (int) $this->getScoutKey(),
            'status_id' => $this->status_id,
            'source_id' => $this->source_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'preferred' => $this->preferred,
            'description' => $this->description,
            'email' => $this->email,
            'email_2' => $this->email_2,
            'mobile' => $this->mobile,
            'sms_opt_out' => $this->sms_opt_out,
            'email_bounce' => $this->email_bounce,
            'phone' => $this->phone,
            'address' => $this->address,
            'address_2' => $this->address_2,
            'birthdate' => $this->birthdate,
            'hsgrad' => (int) $this->hsgrad,
            'assigned_to_id' => $this->assigned_to_id,
            'created_by_id' => $this->created_by_id,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function identifier(): string
    {
        return $this->id;
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequests(): MorphMany
    {
        return $this->morphMany(
            related: ServiceRequest::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ProspectStatus::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ProspectSource::class);
    }

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

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'concern');
    }

    public function alerts(): MorphMany
    {
        return $this->morphMany(Alert::class, 'concern');
    }

    public function careTeam(): MorphToMany
    {
        return $this->morphToMany(
            related: User::class,
            name: 'educatable',
            table: 'care_teams',
        )
            ->using(CareTeam::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function formSubmissions(): MorphMany
    {
        return $this->morphMany(FormSubmission::class, 'author');
    }

    public static function displayNameKey(): string
    {
        return 'full_name';
    }

    public static function displayEmailKey(): string
    {
        return 'email';
    }

    public function getWebPermissions(): Collection
    {
        return collect(['import', ...$this->webPermissions()]);
    }

    public static function filamentResource(): string
    {
        return ProspectResource::class;
    }

    public function subscribedUsers(): MorphToMany
    {
        return $this->morphToMany(
            related: User::class,
            name: 'subscribable',
            table: 'subscriptions',
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes[$this->displayNameKey()],
        );
    }
}
