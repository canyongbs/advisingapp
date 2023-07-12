<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
    /**
     * App\Models\AuditLog
     *
     * @property int $id
     * @property string $description
     * @property int|null $subject_id
     * @property string|null $subject_type
     * @property int|null $user_id
     * @property string|null $properties
     * @property string|null $host
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog query()
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereHost($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereProperties($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereSubjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereSubjectType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|AuditLog whereUserId($value)
     *
     * @mixin \Eloquent
     */
    class IdeHelperAuditLog
    {
    }
}

namespace App\Models{
    /**
     * App\Models\CaseItem
     *
     * @property int $id
     * @property int $casenumber
     * @property string|null $close_details
     * @property string|null $res_details
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property int|null $student_id
     * @property int|null $institution_id
     * @property int|null $state_id
     * @property int|null $type_id
     * @property int|null $priority_id
     * @property int|null $assigned_to_id
     * @property int|null $created_by_id
     * @property-read \App\Models\User|null $assignedTo
     * @property-read \App\Models\User|null $createdBy
     * @property-read \App\Models\Institution|null $institution
     * @property-read \App\Models\CaseItemPriority|null $priority
     * @property-read \App\Models\CaseItemStatus|null $state
     * @property-read \App\Models\RecordStudentItem|null $student
     * @property-read \App\Models\CaseItemType|null $type
     *
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereAssignedToId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereCasenumber($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereCloseDetails($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereCreatedById($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereInstitutionId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem wherePriorityId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereResDetails($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereStateId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereStudentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereTypeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperCaseItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\CaseItemPriority
     *
     * @property int $id
     * @property string $priority
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority query()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority wherePriority($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemPriority withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperCaseItemPriority
    {
    }
}

namespace App\Models{
    /**
     * App\Models\CaseItemStatus
     *
     * @property int $id
     * @property string $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus query()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemStatus withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperCaseItemStatus
    {
    }
}

namespace App\Models{
    /**
     * App\Models\CaseItemType
     *
     * @property int $id
     * @property string $type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType query()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseItemType withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperCaseItemType
    {
    }
}

namespace App\Models{
    /**
     * App\Models\CaseUpdateItem
     *
     * @property int $id
     * @property string $update
     * @property string $internal
     * @property string $direction
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property int|null $student_id
     * @property int|null $case_id
     * @property-read \App\Models\CaseItem|null $case
     * @property-read mixed $direction_label
     * @property-read mixed $internal_label
     * @property-read \App\Models\RecordStudentItem|null $student
     *
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereCaseId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereDirection($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereInternal($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereStudentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereUpdate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|CaseUpdateItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperCaseUpdateItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementEmailItem
     *
     * @property int $id
     * @property string $email
     * @property string $subject
     * @property string $body
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereSubject($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementEmailItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementEmailItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementInteractionDriver
     *
     * @property int $id
     * @property string $driver
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver whereDriver($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionDriver withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementInteractionDriver
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementInteractionItem
     *
     * @property int $id
     * @property string $direction
     * @property string $start
     * @property string $duration
     * @property string $subject
     * @property string|null $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $direction_label
     * @property-read mixed $duration_label
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereDirection($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereDuration($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereStart($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereSubject($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementInteractionItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementInteractionOutcome
     *
     * @property int $id
     * @property string $outcome
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome whereOutcome($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionOutcome withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementInteractionOutcome
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementInteractionRelation
     *
     * @property int $id
     * @property string $relation
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation whereRelation($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionRelation withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementInteractionRelation
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementInteractionType
     *
     * @property int $id
     * @property string|null $type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementInteractionType withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementInteractionType
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementStudentFile
     *
     * @property int $id
     * @property string|null $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property int|null $student_id
     * @property-read mixed $file
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read \App\Models\RecordStudentItem|null $student
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile whereStudentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementStudentFile withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementStudentFile
    {
    }
}

namespace App\Models{
    /**
     * App\Models\EngagementTextItem
     *
     * @property int $id
     * @property string|null $direction
     * @property int $mobile
     * @property string|null $message
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $direction_label
     *
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereDirection($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereMessage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereMobile($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|EngagementTextItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperEngagementTextItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Institution
     *
     * @property int $id
     * @property string|null $code
     * @property string $name
     * @property string|null $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|Institution advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Institution newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Institution onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Institution query()
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Institution withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Institution withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperInstitution
    {
    }
}

namespace App\Models{
    /**
     * App\Models\JourneyEmailItem
     *
     * @property int $id
     * @property string $name
     * @property string $body
     * @property string $start
     * @property string|null $end
     * @property string|null $active
     * @property string $frequency
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $active_label
     * @property-read mixed $frequency_label
     *
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereEnd($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereFrequency($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereStart($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyEmailItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperJourneyEmailItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\JourneyItem
     *
     * @property int $id
     * @property string|null $name
     * @property string $body
     * @property string $start
     * @property string $end
     * @property string $frequency
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $frequency_label
     *
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereEnd($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereFrequency($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereStart($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperJourneyItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\JourneyTargetList
     *
     * @property int $id
     * @property string|null $name
     * @property string|null $description
     * @property string|null $query
     * @property int|null $population
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList query()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList wherePopulation($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereQuery($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTargetList withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperJourneyTargetList
    {
    }
}

namespace App\Models{
    /**
     * App\Models\JourneyTextItem
     *
     * @property int $id
     * @property string|null $name
     * @property string|null $text
     * @property string|null $start
     * @property string|null $end
     * @property string $active
     * @property string|null $frequency
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read mixed $active_label
     * @property-read mixed $frequency_label
     *
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereActive($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereEnd($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereFrequency($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereStart($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereText($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|JourneyTextItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperJourneyTextItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\KbItem
     *
     * @property int $id
     * @property string $question
     * @property string $public
     * @property string|null $solution
     * @property string|null $notes
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property int|null $quality_id
     * @property int|null $status_id
     * @property int|null $category_id
     * @property-read \App\Models\KbItemCategory|null $category
     * @property-read mixed $public_label
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Institution> $institution
     * @property-read int|null $institution_count
     * @property-read \App\Models\KbItemQuality|null $quality
     * @property-read \App\Models\KbItemStatus|null $status
     *
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereCategoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereNotes($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem wherePublic($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereQualityId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereQuestion($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereSolution($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereStatusId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperKbItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\KbItemCategory
     *
     * @property int $id
     * @property string $category
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory query()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory whereCategory($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemCategory withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperKbItemCategory
    {
    }
}

namespace App\Models{
    /**
     * App\Models\KbItemQuality
     *
     * @property int $id
     * @property string $rating
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality query()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality whereRating($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemQuality withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperKbItemQuality
    {
    }
}

namespace App\Models{
    /**
     * App\Models\KbItemStatus
     *
     * @property int $id
     * @property string $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus query()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|KbItemStatus withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperKbItemStatus
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Permission
     *
     * @property int $id
     * @property string $title
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|Permission advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|Permission newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Permission newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Permission onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Permission query()
     * @method static \Illuminate\Database\Eloquent\Builder|Permission whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Permission whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Permission whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Permission whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Permission whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Permission withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Permission withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperPermission
    {
    }
}

namespace App\Models{
    /**
     * App\Models\ProspectItem
     *
     * @property int $id
     * @property string $first
     * @property string $last
     * @property string $full
     * @property string|null $preferred
     * @property string|null $description
     * @property string|null $email
     * @property string|null $email_2
     * @property int|null $mobile
     * @property string|null $sms_opt_out
     * @property string|null $email_bounce
     * @property int|null $phone
     * @property string|null $address
     * @property string|null $address_2
     * @property string|null $birthdate
     * @property string|null $hsgrad
     * @property string|null $hsdate
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property int|null $status_id
     * @property int|null $source_id
     * @property int|null $assigned_to_id
     * @property int|null $created_by_id
     * @property-read \App\Models\User|null $assignedTo
     * @property-read \App\Models\User|null $createdBy
     * @property-read mixed $email_bounce_label
     * @property-read mixed $sms_opt_out_label
     * @property-read \App\Models\ProspectSource|null $source
     * @property-read \App\Models\ProspectStatus|null $status
     *
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereAddress2($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereAssignedToId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereBirthdate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereCreatedById($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereEmail2($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereEmailBounce($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereFirst($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereFull($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereHsdate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereHsgrad($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereLast($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereMobile($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem wherePhone($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem wherePreferred($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereSmsOptOut($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereSourceId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereStatusId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperProspectItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\ProspectSource
     *
     * @property int $id
     * @property string $source
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource query()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereSource($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectSource withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperProspectSource
    {
    }
}

namespace App\Models{
    /**
     * App\Models\ProspectStatus
     *
     * @property int $id
     * @property string $status
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus query()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProspectStatus withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperProspectStatus
    {
    }
}

namespace App\Models{
    /**
     * App\Models\RecordEnrollmentItem
     *
     * @property int $id
     * @property string $sisid
     * @property string|null $name
     * @property string|null $start
     * @property string|null $end
     * @property string|null $course
     * @property float|null $grade
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereCourse($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereEnd($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereGrade($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereSisid($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereStart($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordEnrollmentItem withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperRecordEnrollmentItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\RecordProgramItem
     *
     * @property int $id
     * @property string $name
     * @property string|null $institution
     * @property string|null $plan
     * @property string|null $career
     * @property string|null $term
     * @property string|null $status
     * @property string|null $foi
     * @property float|null $gpa
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read mixed $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereCareer($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereFoi($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereGpa($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereInstitution($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem wherePlan($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereTerm($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordProgramItem whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class IdeHelperRecordProgramItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\RecordStudentItem
     *
     * @property int $id
     * @property string $sisid
     * @property string|null $otherid
     * @property string|null $first
     * @property string|null $last
     * @property string|null $full
     * @property string|null $preferred
     * @property string|null $email
     * @property string|null $email_2
     * @property int|null $mobile
     * @property string|null $sms_opt_out
     * @property string|null $email_bounce
     * @property string|null $phone
     * @property string|null $address
     * @property string|null $address_2
     * @property string|null $birthdate
     * @property int|null $hsgrad
     * @property string|null $dual
     * @property string|null $ferpa
     * @property float|null $gpa
     * @property string|null $dfw
     * @property string|null $firstgen
     * @property string|null $ethnicity
     * @property string|null $lastlmslogin
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read mixed $deleted_at
     * @property-read mixed $dual_label
     * @property-read mixed $email_bounce_label
     * @property-read mixed $ferpa_label
     * @property-read mixed $firstgen_label
     * @property-read mixed $sms_opt_out_label
     *
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem query()
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereAddress($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereAddress2($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereBirthdate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereDfw($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereDual($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereEmail2($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereEmailBounce($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereEthnicity($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereFerpa($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereFirst($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereFirstgen($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereFull($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereGpa($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereHsgrad($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereLast($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereLastlmslogin($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereMobile($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereOtherid($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem wherePhone($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem wherePreferred($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereSisid($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereSmsOptOut($value)
     * @method static \Illuminate\Database\Eloquent\Builder|RecordStudentItem whereUpdatedAt($value)
     *
     * @mixin \Eloquent
     */
    class IdeHelperRecordStudentItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\ReportProspect
     *
     * @method static \Illuminate\Database\Eloquent\Builder|ReportProspect newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ReportProspect newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ReportProspect query()
     *
     * @mixin \Eloquent
     */
    class IdeHelperReportProspect
    {
    }
}

namespace App\Models{
    /**
     * App\Models\ReportStudent
     *
     * @method static \Illuminate\Database\Eloquent\Builder|ReportStudent newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ReportStudent newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ReportStudent query()
     *
     * @mixin \Eloquent
     */
    class IdeHelperReportStudent
    {
    }
}

namespace App\Models{
    /**
     * App\Models\Role
     *
     * @property int $id
     * @property string|null $title
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder|Role advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Role onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Role query()
     * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Role whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Role whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Role withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Role withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperRole
    {
    }
}

namespace App\Models{
    /**
     * App\Models\SupportFeedbackItem
     *
     * @method static \Illuminate\Database\Eloquent\Builder|SupportFeedbackItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportFeedbackItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportFeedbackItem query()
     *
     * @mixin \Eloquent
     */
    class IdeHelperSupportFeedbackItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\SupportItem
     *
     * @method static \Illuminate\Database\Eloquent\Builder|SupportItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportItem query()
     *
     * @mixin \Eloquent
     */
    class IdeHelperSupportItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\SupportPage
     *
     * @property int $id
     * @property string $title
     * @property string $body
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     *
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage query()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage whereBody($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportPage withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperSupportPage
    {
    }
}

namespace App\Models{
    /**
     * App\Models\SupportTrainingItem
     *
     * @method static \Illuminate\Database\Eloquent\Builder|SupportTrainingItem newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportTrainingItem newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|SupportTrainingItem query()
     *
     * @mixin \Eloquent
     */
    class IdeHelperSupportTrainingItem
    {
    }
}

namespace App\Models{
    /**
     * App\Models\User
     *
     * @property int $id
     * @property string|null $emplid
     * @property string|null $name
     * @property string|null $email
     * @property string|null $email_verified_at
     * @property string|null $password
     * @property string|null $remember_token
     * @property string|null $locale
     * @property string|null $type
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UserAlert> $alerts
     * @property-read int|null $alerts_count
     * @property-read mixed $is_admin
     * @property-read mixed $type_label
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
     * @property-read int|null $roles_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder|User admins()
     * @method static \Illuminate\Database\Eloquent\Builder|User advancedFilter($data)
     * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|User query()
     * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereEmplid($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereLocale($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperUser
    {
    }
}

namespace App\Models{
    /**
     * App\Models\UserAlert
     *
     * @property int $id
     * @property string|null $message
     * @property string|null $link
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
     * @property-read int|null $users_count
     *
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert advancedFilter($data)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert query()
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert whereLink($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert whereMessage($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|UserAlert withoutTrashed()
     *
     * @mixin \Eloquent
     */
    class IdeHelperUserAlert
    {
    }
}
