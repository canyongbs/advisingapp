<?php

namespace Assist\AssistDataModel\Models;

use Eloquent;
use Assist\Case\Models\CaseItem;
use Illuminate\Database\Eloquent\Model;
use Assist\Engagement\Models\Engagement;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Assist\Engagement\Models\EngagementFile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Assist\Authorization\Models\Concerns\DefinesPermissions;
use Assist\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * Assist\AssistDataModel\Models\Student
 *
 * @property-read Collection<int, \Assist\Audit\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, CaseItem> $cases
 * @property-read int|null $cases_count
 * @property-read Collection<int, EngagementFile> $engagementFiles
 * @property-read int|null $engagement_files_count
 * @property-read Collection<int, Engagement> $engagements
 * @property-read int|null $engagements_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 *
 * @method static \Assist\AssistDataModel\Database\Factories\StudentFactory factory($count = null, $state = [])
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 *
 * @mixin Eloquent
 */
class Student extends Model implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use DefinesPermissions;
    use Notifiable;

    protected $primaryKey = 'sisid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'sisid' => 'string',
    ];

    public function cases(): MorphMany
    {
        return $this->morphMany(
            related: CaseItem::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'sisid'
        );
    }

    public function engagements(): MorphMany
    {
        return $this->morphMany(
            related: Engagement::class,
            name: 'recipient',
        );
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
        );
    }
}
