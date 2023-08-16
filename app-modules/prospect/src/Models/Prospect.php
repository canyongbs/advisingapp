<?php

namespace Assist\Prospect\Models;

use Eloquent;
use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Illuminate\Support\Carbon;
use Assist\Case\Models\CaseItem;
use OwenIt\Auditing\Models\Audit;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\Engagement\Models\EngagementFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Assist\Prospect\Database\Factories\ProspectFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\Prospect\Models\Prospect
 *
 * @property string $id
 * @property string $status_id
 * @property string $source_id
 * @property string $first_name
 * @property string $last_name
 * @property string $full
 * @property string|null $preferred
 * @property string|null $description
 * @property string|null $email
 * @property string|null $email_2
 * @property string|null $mobile
 * @property bool $sms_opt_out
 * @property bool $email_bounce
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $address_2
 * @property string|null $birthdate
 * @property string|null $hsgrad
 * @property string|null $assigned_to_id
 * @property string|null $created_by_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User|null $assignedTo
 * @property-read Collection<int, Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, CaseItem> $cases
 * @property-read int|null $cases_count
 * @property-read User|null $createdBy
 * @property-read Collection<int, EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read ProspectSource $source
 * @property-read ProspectStatus $status
 *
 * @method static ProspectFactory factory($count = null, $state = [])
 * @method static Builder|Prospect newModelQuery()
 * @method static Builder|Prospect newQuery()
 * @method static Builder|Prospect onlyTrashed()
 * @method static Builder|Prospect query()
 * @method static Builder|Prospect whereAddress($value)
 * @method static Builder|Prospect whereAddress2($value)
 * @method static Builder|Prospect whereAssignedToId($value)
 * @method static Builder|Prospect whereBirthdate($value)
 * @method static Builder|Prospect whereCreatedAt($value)
 * @method static Builder|Prospect whereCreatedById($value)
 * @method static Builder|Prospect whereDeletedAt($value)
 * @method static Builder|Prospect whereDescription($value)
 * @method static Builder|Prospect whereEmail($value)
 * @method static Builder|Prospect whereEmail2($value)
 * @method static Builder|Prospect whereEmailBounce($value)
 * @method static Builder|Prospect whereFirstName($value)
 * @method static Builder|Prospect whereFull($value)
 * @method static Builder|Prospect whereHsgrad($value)
 * @method static Builder|Prospect whereId($value)
 * @method static Builder|Prospect whereLastName($value)
 * @method static Builder|Prospect whereMobile($value)
 * @method static Builder|Prospect wherePhone($value)
 * @method static Builder|Prospect wherePreferred($value)
 * @method static Builder|Prospect whereSmsOptOut($value)
 * @method static Builder|Prospect whereSourceId($value)
 * @method static Builder|Prospect whereStatusId($value)
 * @method static Builder|Prospect whereUpdatedAt($value)
 * @method static Builder|Prospect withTrashed()
 * @method static Builder|Prospect withoutTrashed()
 *
 * @mixin Eloquent
 */
class Prospect extends BaseModel implements Auditable
{
    use HasUuids;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'first_name',
        'last_name',
        'full',
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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cases(): MorphMany
    {
        return $this->morphMany(
            related: CaseItem::class,
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
            relation: 'prospects',
        );
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
