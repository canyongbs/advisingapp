<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Models;

use AdvisingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AdvisingApp\CaseManagement\Enums\CaseTypeAssignmentTypes;
use AdvisingApp\Team\Models\Team;
use App\Models\BaseModel;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperCaseType
 */
class CaseType extends BaseModel implements Auditable
{
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'has_enabled_feedback_collection',
        'has_enabled_csat',
        'has_enabled_nps',
        'is_managers_case_created_email_enabled',
        'is_managers_case_created_notification_enabled',
        'is_managers_case_assigned_email_enabled',
        'is_managers_case_assigned_notification_enabled',
        'is_managers_case_closed_email_enabled',
        'is_managers_case_closed_notification_enabled',
        'is_auditors_case_created_email_enabled',
        'is_auditors_case_created_notification_enabled',
        'is_auditors_case_assigned_email_enabled',
        'is_auditors_case_assigned_notification_enabled',
        'is_auditors_case_closed_email_enabled',
        'is_auditors_case_closed_notification_enabled',
        'is_managers_case_update_email_enabled',
        'is_managers_case_update_notification_enabled',
        'is_managers_case_status_change_email_enabled',
        'is_managers_case_status_change_notification_enabled',
        'is_auditors_case_update_email_enabled',
        'is_auditors_case_update_notification_enabled',
        'is_auditors_case_status_change_email_enabled',
        'is_auditors_case_status_change_notification_enabled',
        'is_customers_case_created_email_enabled',
        'is_customers_case_created_notification_enabled',
        'is_customers_case_assigned_email_enabled',
        'is_customers_case_assigned_notification_enabled',
        'is_customers_case_update_email_enabled',
        'is_customers_case_update_notification_enabled',
        'is_customers_case_status_change_email_enabled',
        'is_customers_case_status_change_notification_enabled',
        'is_customers_case_closed_email_enabled',
        'is_customers_case_closed_notification_enabled',
        'is_customers_survey_response_email_enabled',
        'assignment_type',
    ];

    protected $casts = [
        'has_enabled_feedback_collection' => 'boolean',
        'has_enabled_csat' => 'boolean',
        'has_enabled_nps' => 'boolean',
        'is_managers_case_created_email_enabled' => 'boolean',
        'is_managers_case_created_notification_enabled' => 'boolean',
        'is_managers_case_assigned_email_enabled' => 'boolean',
        'is_managers_case_assigned_notification_enabled' => 'boolean',
        'is_managers_case_closed_email_enabled' => 'boolean',
        'is_managers_case_closed_notification_enabled' => 'boolean',
        'is_auditors_case_created_email_enabled' => 'boolean',
        'is_auditors_case_created_notification_enabled' => 'boolean',
        'is_auditors_case_assigned_email_enabled' => 'boolean',
        'is_auditors_case_assigned_notification_enabled' => 'boolean',
        'is_auditors_case_closed_email_enabled' => 'boolean',
        'is_auditors_case_closed_notification_enabled' => 'boolean',
        'is_managers_case_update_email_enabled' => 'boolean',
        'is_managers_case_update_notification_enabled' => 'boolean',
        'is_managers_case_status_change_email_enabled' => 'boolean',
        'is_managers_case_status_change_notification_enabled' => 'boolean',
        'is_auditors_case_update_email_enabled' => 'boolean',
        'is_auditors_case_update_notification_enabled' => 'boolean',
        'is_auditors_case_status_change_email_enabled' => 'boolean',
        'is_auditors_case_status_change_notification_enabled' => 'boolean',
        'is_customers_case_created_email_enabled' => 'boolean',
        'is_customers_case_created_notification_enabled' => 'boolean',
        'is_customers_case_assigned_email_enabled' => 'boolean',
        'is_customers_case_assigned_notification_enabled' => 'boolean',
        'is_customers_case_update_email_enabled' => 'boolean',
        'is_customers_case_update_notification_enabled' => 'boolean',
        'is_customers_case_status_change_email_enabled' => 'boolean',
        'is_customers_case_status_change_notification_enabled' => 'boolean',
        'is_customers_case_closed_email_enabled' => 'boolean',
        'is_customers_case_closed_notification_enabled' => 'boolean',
        'is_customers_survey_response_email_enabled' => 'boolean',
        'assignment_type' => CaseTypeAssignmentTypes::class,
    ];

    public function getTable()
    {
        return 'case_types';
    }

    public function cases(): HasManyThrough
    {
        return $this->through('priorities')->has('cases');
    }

    /**
     * @return HasMany<CasePriority, $this>
     */
    public function priorities(): HasMany
    {
        return $this->hasMany(CasePriority::class, 'type_id');
    }

    /**
     * @return HasOne<CaseForm, $this>
     */
    public function form(): HasOne
    {
        return $this->hasOne(CaseForm::class, 'case_type_id');
    }

    /**
     * @return BelongsToMany<Team, $this, covariant CaseTypeManager>
     */
    public function managers(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'case_type_managers', 'case_type_id', 'team_id')
            ->using(CaseTypeManager::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant CaseTypeAuditor>
     */
    public function auditors(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'case_type_auditors', 'case_type_id', 'team_id')
            ->using(CaseTypeAuditor::class)
            ->withTimestamps();
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
